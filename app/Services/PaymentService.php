<?php

namespace App\Services;

use App\Models\User;
use App\Models\Membership;
use App\Models\Transaction;
use App\Models\Referral;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentService
{
    private string $baseUrl;
    private string $secretKey;
    private string $publicKey;
    private string $webhookSecret;

    public function __construct()
    {
        $this->baseUrl = config('paymongo.base_url');
        $this->secretKey = config('paymongo.secret_key');
        $this->publicKey = config('paymongo.public_key');
        $this->webhookSecret = config('paymongo.webhook_secret');
    }

    /**
     * Create a payment intent for membership upgrade.
     */
    public function createPaymentIntent(User $user, string $tier): array
    {
        try {
            $amount = config("paymongo.membership_prices.{$tier}");
            
            if (!$amount) {
                throw new Exception("Invalid membership tier: {$tier}");
            }

            // Create transaction record
            $transaction = Transaction::createMembershipUpgrade($user, $tier, $amount / 100);

            // Create membership record
            $membership = Membership::create([
                'user_id' => $user->id,
                'tier' => $tier,
                'amount' => $amount / 100,
                'transaction_id' => $transaction->id,
                'payment_status' => 'pending',
            ]);

            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/payment_intents", [
                    'data' => [
                        'attributes' => [
                            'amount' => $amount,
                            'currency' => 'PHP',
                            'description' => "ShoPilipinas {$tier} VIP Membership - {$user->name}",
                            'statement_descriptor' => 'ShoPilipinas VIP',
                            'metadata' => [
                                'user_id' => $user->id,
                                'membership_tier' => $tier,
                                'transaction_id' => $transaction->id,
                                'membership_id' => $membership->id,
                            ],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('PayMongo payment intent creation failed', [
                    'response' => $response->json(),
                    'user_id' => $user->id,
                    'tier' => $tier,
                ]);
                throw new Exception('Failed to create payment intent');
            }

            $paymentIntent = $response->json()['data'];
            
            // Update transaction with PayMongo payment ID
            $transaction->update([
                'external_payment_id' => $paymentIntent['id'],
                'payment_metadata' => $paymentIntent,
            ]);

            // Update membership with PayMongo payment ID
            $membership->update([
                'paymongo_payment_id' => $paymentIntent['id'],
                'payment_details' => $paymentIntent,
            ]);

            return [
                'success' => true,
                'payment_intent' => $paymentIntent,
                'client_key' => $paymentIntent['attributes']['client_key'],
                'transaction_id' => $transaction->id,
                'membership_id' => $membership->id,
            ];

        } catch (Exception $e) {
            Log::error('Payment intent creation error', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'tier' => $tier,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create payment method for the payment intent.
     */
    public function createPaymentMethod(string $type, array $details): array
    {
        try {
            $response = Http::withBasicAuth($this->publicKey, '')
                ->post("{$this->baseUrl}/payment_methods", [
                    'data' => [
                        'attributes' => [
                            'type' => $type,
                            'details' => $details,
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('PayMongo payment method creation failed', [
                    'response' => $response->json(),
                    'type' => $type,
                ]);
                throw new Exception('Failed to create payment method');
            }

            return [
                'success' => true,
                'payment_method' => $response->json()['data'],
            ];

        } catch (Exception $e) {
            Log::error('Payment method creation error', [
                'error' => $e->getMessage(),
                'type' => $type,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Attach payment method to payment intent.
     */
    public function attachPaymentMethod(string $paymentIntentId, string $paymentMethodId): array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/payment_intents/{$paymentIntentId}/attach", [
                    'data' => [
                        'attributes' => [
                            'payment_method' => $paymentMethodId,
                            'client_key' => request('client_key'),
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('PayMongo payment method attachment failed', [
                    'response' => $response->json(),
                    'payment_intent_id' => $paymentIntentId,
                    'payment_method_id' => $paymentMethodId,
                ]);
                throw new Exception('Failed to attach payment method');
            }

            return [
                'success' => true,
                'payment_intent' => $response->json()['data'],
            ];

        } catch (Exception $e) {
            Log::error('Payment method attachment error', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
                'payment_method_id' => $paymentMethodId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process webhook from PayMongo.
     */
    public function processWebhook(array $payload): bool
    {
        try {
            $event = $payload['data'];
            $eventType = $event['attributes']['type'];

            Log::info('PayMongo webhook received', [
                'event_type' => $eventType,
                'event_id' => $event['id'],
            ]);

            switch ($eventType) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentSuccess($event['attributes']['data']);
                
                case 'payment_intent.payment_failed':
                    return $this->handlePaymentFailed($event['attributes']['data']);
                
                default:
                    Log::info('Unhandled webhook event type', ['type' => $eventType]);
                    return true;
            }

        } catch (Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            return false;
        }
    }

    /**
     * Handle successful payment.
     */
    private function handlePaymentSuccess(array $paymentData): bool
    {
        try {
            $metadata = $paymentData['attributes']['metadata'] ?? [];
            $userId = $metadata['user_id'] ?? null;
            $membershipId = $metadata['membership_id'] ?? null;
            $transactionId = $metadata['transaction_id'] ?? null;

            if (!$userId || !$membershipId || !$transactionId) {
                Log::error('Missing metadata in payment success webhook', $metadata);
                return false;
            }

            $user = User::find($userId);
            $membership = Membership::find($membershipId);
            $transaction = Transaction::find($transactionId);

            if (!$user || !$membership || !$transaction) {
                Log::error('Related records not found for payment success', [
                    'user_id' => $userId,
                    'membership_id' => $membershipId,
                    'transaction_id' => $transactionId,
                ]);
                return false;
            }

            // Mark membership as completed
            $membership->markAsCompleted();
            
            // Mark transaction as completed
            $transaction->markAsCompleted();

            // Process referral commissions if user was referred
            $this->processReferralCommissions($user, $membership->tier, $membership->amount);

            Log::info('Payment processed successfully', [
                'user_id' => $userId,
                'membership_tier' => $membership->tier,
                'amount' => $membership->amount,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Payment success handling error', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData,
            ]);
            return false;
        }
    }

    /**
     * Handle failed payment.
     */
    private function handlePaymentFailed(array $paymentData): bool
    {
        try {
            $metadata = $paymentData['attributes']['metadata'] ?? [];
            $membershipId = $metadata['membership_id'] ?? null;
            $transactionId = $metadata['transaction_id'] ?? null;

            if ($membershipId) {
                $membership = Membership::find($membershipId);
                $membership?->markAsFailed();
            }

            if ($transactionId) {
                $transaction = Transaction::find($transactionId);
                $failureReason = $paymentData['attributes']['last_payment_error']['message'] ?? 'Payment failed';
                $transaction?->markAsFailed($failureReason);
            }

            Log::info('Payment failure processed', [
                'membership_id' => $membershipId,
                'transaction_id' => $transactionId,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Payment failure handling error', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData,
            ]);
            return false;
        }
    }

    /**
     * Process referral commissions.
     */
    private function processReferralCommissions(User $user, string $tier, float $amount): void
    {
        try {
            $referral = $user->referralsReceived()->first();
            
            if (!$referral) {
                return; // User was not referred
            }

            $referrer = $referral->referrer;
            
            // Check if referrer can refer this tier
            if ($tier === 'diamond' && !$referrer->canReferDiamond()) {
                Log::warning('Free user attempted to refer Diamond tier', [
                    'referrer_id' => $referrer->id,
                    'referred_id' => $user->id,
                    'tier' => $tier,
                ]);
                return;
            }

            // Calculate and update commission
            $commission = Referral::calculateCommission($referrer, $tier, $amount);
            
            $referral->update([
                'membership_tier_referred' => $tier,
                'commission_rate' => $commission['rate'],
                'commission_amount' => $commission['amount'],
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            Log::info('Referral commission processed', [
                'referrer_id' => $referrer->id,
                'referred_id' => $user->id,
                'tier' => $tier,
                'commission_rate' => $commission['rate'],
                'commission_amount' => $commission['amount'],
            ]);

        } catch (Exception $e) {
            Log::error('Referral commission processing error', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'tier' => $tier,
            ]);
        }
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }
}
