<?php

namespace App\Utils\Services\v1\Wallet;

use App\Exceptions\ClientErrorException;
use App\Jobs\SendNotificationJob;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\TransactionType;
use App\Http\Resources\WalletTransactionResource;
use App\Models\Invoice;
use App\Notifications\WalletFundedNotification;
use App\Notifications\WalletFundingFailedNotification;
use App\Notifications\WalletTransactionNotification;
use App\Utils\Enums\TransactionStatusEnum;
use App\Utils\Enums\TransactionTypeEnum;
use App\Utils\Service\V1\Payment\PaystackService as PaymentPaystackService;
use App\Utils\Service\V1\VirtualAccountService\VirtualAccountService;
use App\Utils\Services\Integrations\PaystackService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class WalletService
{
    protected PaymentPaystackService $paystackService;
    protected VirtualAccountService $virtualAccountService;

    public function __construct(PaymentPaystackService $paystackService, VirtualAccountService $virtualAccountService)
    {
        $this->paystackService = $paystackService;
        $this->virtualAccountService = $virtualAccountService;
    }

    /**
     * Generate a unique transaction reference
     * @return string
     */
    private function generateTransactionRef(): string
    {
        return 'WLT_' . Str::random(16);
    }

    /**
     * Get wallet details for authenticated user
     *
     * @return array
     * @throws ClientErrorException
     */
    public function getWalletDetails()
    {
        $user = auth('web')->user();
        try {
            // Get virtual account details
            $virtualAccountDetails = $this->virtualAccountService->getUserVirtualAccountDetails($user);

            return [
                'wallet_balance' => $user->wallet_balance,
                'currency' => 'NGN',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'virtual_account' => $virtualAccountDetails['virtual_account'],
                'has_virtual_account' => $virtualAccountDetails['has_virtual_account'],
                'virtual_account_status' => $virtualAccountDetails['account_status'],
                'virtual_account_active' => $virtualAccountDetails['is_active'],
            ];
        } catch (\Exception $e) {
            Log::error('WalletService: Failed to get wallet details', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Initialize wallet funding
     *
     * @param float $amount
     * @param string $email
     * @return array
     * @throws ClientErrorException
     */
    public function initializeWalletFunding(float $amount, string $email): array
    {
        try {
            $user = auth(USER_GUARD)->user();

            if ($amount <= 0) {
                throw new ClientErrorException('Amount must be greater than zero', 400);
            }

            // Generate unique reference
            $reference = 'WLT_' . Str::random(16);

            // Create wallet transaction record
            $transaction = WalletTransaction::create([
                'transaction_ref' => $reference,
                'user_id' => $user->id,
                'action_type' => WalletTransaction::ACTION_TYPE_CREDIT,
                'transaction_type_id' => TransactionTypeEnum::WALLET_TOPUP->value,
                'description' => 'Wallet funding',
                'amount' => $amount,
                'wallet_balance_before' => $user->wallet_balance,
                'wallet_balance_after' => $user->wallet_balance,
                'status' => TransactionStatusEnum::PENDING->value,
                'meta_data' => [
                    'payment_provider' => 'paystack',
                    'email' => $email,
                ],
            ]);

            // Initialize payment with Paystack
            $callbackUrl = config('app.frontend_url') . '/wallet/funding/callback';
            $paymentData = $this->paystackService->initializePayment(
                $user,
                $amount,
                $email,
                $reference,
                $callbackUrl
            );

            return [
                'transaction_ref' => $reference,
                'authorization_url' => $paymentData['authorization_url'],
                'access_code' => $paymentData['access_code'],
                'amount' => $amount,
                'currency' => 'NGN',
            ];
        } catch (\Exception $e) {
            Log::error('WalletService: Failed to initialize wallet funding', [
                'error' => $e->getMessage(),
                'user_id' => auth(USER_GUARD)->id(),
                'amount' => $amount,
            ]);
            throw new ClientErrorException('Unable to initialize wallet funding: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify payment and credit wallet
     *
     * @param string $reference
     * @return array
     * @throws ClientErrorException
     */
    public function verifyPaymentAndCreditWallet(string $reference): array
    {
        try {
            $transaction = WalletTransaction::where('transaction_ref', $reference)->first();

            if (!$transaction) {
                throw new ClientErrorException('Transaction not found', 404);
            }

            if ($transaction->status === TransactionStatusEnum::SUCCESSFUL->value) {
                return [
                    'message' => 'Payment already verified',
                    'transaction' => new WalletTransactionResource($transaction),
                ];
            }

            // Verify payment with Paystack
            $paymentData = $this->paystackService->verifyPayment($reference);

            // Handle different payment statuses
            switch ($paymentData['status']) {
                case 'success':
                    return $this->processSuccessfulPayment($transaction, $paymentData);

                case 'failed':
                    // Payment failed - update transaction status
                    $transaction->update([
                        'status' => TransactionStatusEnum::FAILED->value,
                        'meta_data' => array_merge($transaction->meta_data ?? [], [
                            'paystack_transaction_id' => $paymentData['id'],
                            'gateway_ref' => $paymentData['reference'],
                            'gateway_response' => $paymentData['gateway_response'] ?? 'Payment failed',
                            'failed_at' => now(),
                            'ip' => $paymentData['ip_address'] ?? '',
                        ]),
                    ]);

                    // Send notification to user about failed payment
                    $user = $transaction->user;
                    if ($user->email) {
                        $failureReason = $paymentData['gateway_response'] ?? 'Payment failed';
                        SendNotificationJob::dispatch($user, new WalletFundingFailedNotification($transaction->fresh(), $failureReason));
                    }

                    throw new ClientErrorException(
                        'Payment failed: ' . ($paymentData['gateway_response'] ?? 'Unknown error'),
                        400
                    );

                case 'abandoned':
                    // Payment abandoned - update transaction status
                    $transaction->update([
                        'status' => TransactionStatusEnum::CANCELLED->value,
                        'meta_data' => array_merge($transaction->meta_data ?? [], [
                            'paystack_transaction_id' => $paymentData['id'],
                            'gateway_ref' => $paymentData['reference'],
                            'abandoned_at' => now(),
                        ]),
                    ]);

                    throw new ClientErrorException('Payment was abandoned by user', 400);

                case 'pending':
                    $transaction->update([
                        'meta_data' => array_merge($transaction->meta_data ?? [], [
                            'paystack_transaction_id' => $paymentData['id'],
                            'gateway_ref' => $paymentData['reference'],
                            'last_checked' => now(),
                        ]),
                    ]);

                    throw new ClientErrorException('Payment is still pending. Please try again later.', 400);

                default:
                    // Unknown status
                    $transaction->update([
                        'status' => TransactionStatusEnum::FAILED->value,
                        'meta_data' => array_merge($transaction->meta_data ?? [], [
                            'paystack_transaction_id' => $paymentData['id'],
                            'gateway_ref' => $paymentData['reference'],
                            'unknown_status' => $paymentData['status'],
                            'failed_at' => now(),
                        ]),
                    ]);

                    throw new ClientErrorException('Payment verification failed: Unknown status', 400);
            }
        } catch (\Exception $e) {
            Log::error('WalletService: Failed to verify payment', [
                'error' => $e->getMessage(),
                'reference' => $reference,
            ]);
            throw new ClientErrorException('Unable to verify payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process successful payment
     *
     * @param WalletTransaction $transaction
     * @param array $paymentData
     * @return array
     */
    private function processSuccessfulPayment(WalletTransaction $transaction, array $paymentData): array
    {
        // Credit wallet in database transaction
        DB::transaction(function () use ($transaction, $paymentData) {
            $user = $transaction->user;
            $newBalance = $user->wallet_balance + $transaction->amount;

            // Update user wallet balance
            $user->update(['wallet_balance' => $newBalance]);

            // Update transaction
            $transaction->update([
                'status' => TransactionStatusEnum::SUCCESSFUL->value,
                'wallet_balance_after' => $newBalance,
                'meta_data' => array_merge($transaction->meta_data ?? [], [
                    'paystack_transaction_id' => $paymentData['id'],
                    'gateway_ref' => $paymentData['reference'],
                    'paid_at' => $paymentData['paid_at'],
                    'channel' => $paymentData['channel'] ?? null,
                    'ip_address' => $paymentData['ip_address'] ?? null,
                ]),
            ]);

            // Send notification to user
            if ($user->email) {
                SendNotificationJob::dispatch($user, new WalletFundedNotification($transaction->fresh()));
            }
        });

        return [
            'message' => 'Payment verified and wallet credited successfully',
            'transaction' => new WalletTransactionResource($transaction->fresh()),
            'new_balance' => $transaction->user->wallet_balance,
        ];
    }

    /**
     * Get wallet transaction history
     *
     * @param int $perPage
     * @param Request|null $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     * @throws ClientErrorException
     */
    public function getTransactionHistory(int $perPage = 20, ?Request $request = null): \Illuminate\Pagination\LengthAwarePaginator
    {
        try {
            $user = auth(USER_GUARD)->user();

            $query = QueryBuilder::for(WalletTransaction::class)
                ->where('user_id', $user->id)
                ->with(['transactionType', 'invoice'])
                ->allowedFilters([
                    AllowedFilter::exact('status'),
                    AllowedFilter::exact('action_type'),
                    AllowedFilter::scope('search'),
                    AllowedFilter::scope('amount_range'),
                    AllowedFilter::scope('date_range'),
                    AllowedFilter::scope('transaction_type'),
                ])
                ->allowedSorts([
                    'created_at',
                    'updated_at',
                    'amount',
                    'status',
                    'action_type',
                ])
                ->defaultSort('-created_at');

            $transactions = $query->paginate($perPage);

            // Transform the paginated results
            $transactions->getCollection()->transform(function ($transaction) {
                return new WalletTransactionResource($transaction);
            });

            return $transactions;
        } catch (\Exception $e) {
            Log::error('WalletService: Failed to get transaction history', [
                'error' => $e->getMessage(),
                'user_id' => auth(USER_GUARD)->id(),
            ]);
            throw new ClientErrorException('Unable to get transaction history: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get transaction details
     *
     * @param string $transactionId
     * @return array
     * @throws ClientErrorException
     */
    public function getTransactionDetails(string $transactionId): array
    {
        try {
            $user = auth(USER_GUARD)->user();

            $transaction = WalletTransaction::where('user_id', $user->id)
                ->where('id', $transactionId)
                ->with(['transactionType', 'invoice'])
                ->first();

            if (!$transaction) throw new ClientErrorException('Transaction not found', 404);

            return [
                'transaction' => new WalletTransactionResource($transaction),
                'detailed_meta_data' => $transaction->meta_data,
                'status_display_name' => $this->getStatusDisplayName($transaction->status),
                'action_type_display_name' => $this->getActionTypeDisplayName($transaction->action_type),
            ];
        } catch (\Exception $e) {
            Log::error('WalletService: Failed to get transaction details', [
                'error' => $e->getMessage(),
                'user_id' => auth(USER_GUARD)->id(),
                'transaction_id' => $transactionId,
            ]);
            throw new ClientErrorException('Unable to get transaction details: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get status display name
     *
     * @param int $status
     * @return string
     */
    private function getStatusDisplayName(int $status): string
    {
        return match ($status) {
            1 => 'Pending',
            2 => 'Successful',
            3 => 'Failed',
            default => 'Unknown',
        };
    }

    /**
     * Get action type display name
     *
     * @param string $actionType
     * @return string
     */
    private function getActionTypeDisplayName(string $actionType): string
    {
        return match ($actionType) {
            'credit' => 'Credit',
            'debit' => 'Debit',
            default => ucfirst($actionType),
        };
    }

    /**
     * Process webhook from Paystack
     *
     * @param array $payload
     * @param string $signature
     * @return array
     * @throws ClientErrorException
     */
    public function processWebhook(array $payload, string $signature): array
    {
        try {
            // Verify webhook signature
            if (!$this->paystackService->verifyWebhookSignature(json_encode($payload), $signature)) {
                throw new ClientErrorException('Invalid webhook signature', 400);
            }

            $event = $payload['event'] ?? '';
            $data = $payload['data'] ?? [];

            switch ($event) {
                case 'charge.success':
                    $reference = $data['reference'] ?? '';
                    return $this->verifyPaymentAndCreditWallet($reference);

                case 'charge.failed':
                    $reference = $data['reference'] ?? '';
                    return $this->processFailedPayment($reference, $data);

                case 'charge.abandoned':
                    $reference = $data['reference'] ?? '';
                    return $this->processAbandonedPayment($reference, $data);

                case 'dedicated_account.assigned':
                case 'dedicated_account.assigned_failed':
                case 'charge.success':
                    // Handle virtual account related events
                    return $this->virtualAccountService->processVirtualAccountWebhook($payload, $signature);

                default:
                    return [
                        'message' => 'Webhook processed successfully',
                        'event' => $event,
                        'note' => 'Event not handled for wallet operations'
                    ];
            }
        } catch (\Exception $e) {
            Log::error('WalletService: Webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            throw new ClientErrorException('Unable to process webhook: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process failed payment from webhook
     *
     * @param string $reference
     * @param array $data
     * @return array
     */
    private function processFailedPayment(string $reference, array $data): array
    {
        $transaction = WalletTransaction::where('transaction_ref', $reference)->first();

        if (!$transaction) {
            throw new ClientErrorException('Transaction not found', 404);
        }

        $transaction->update([
            'status' => TransactionStatusEnum::FAILED->value,
            'meta_data' => array_merge($transaction->meta_data ?? [], [
                'paystack_transaction_id' => $data['id'],
                'gateway_ref' => $data['reference'],
                'gateway_response' => $data['gateway_response'] ?? 'Payment failed',
                'failed_at' => now(),
                'webhook_processed' => true,
            ]),
        ]);

        // Send notification to user about failed payment
        $user = $transaction->user;
        if ($user->email) {
            $failureReason = $data['gateway_response'] ?? 'Payment failed';
            SendNotificationJob::dispatch($user, new WalletFundingFailedNotification($transaction->fresh(), $failureReason));
        }

        return [
            'message' => 'Failed payment processed successfully',
            'transaction' => new WalletTransactionResource($transaction->fresh()),
        ];
    }

    /**
     * Process abandoned payment from webhook
     *
     * @param string $reference
     * @param array $data
     * @return array
     */
    private function processAbandonedPayment(string $reference, array $data): array
    {
        $transaction = WalletTransaction::where('transaction_ref', $reference)->first();

        if (!$transaction) {
            throw new ClientErrorException('Transaction not found', 404);
        }

        $transaction->update([
            'status' => TransactionStatusEnum::CANCELLED->value,
            'meta_data' => array_merge($transaction->meta_data ?? [], [
                'paystack_transaction_id' => $data['id'],
                'gateway_ref' => $data['reference'],
                'abandoned_at' => now(),
                'webhook_processed' => true,
            ]),
        ]);

        return [
            'message' => 'Abandoned payment processed successfully',
            'transaction' => new WalletTransactionResource($transaction->fresh()),
        ];
    }

    /**
     * Process invoice payment
     *
     * @param WalletTransaction $transaction
     * @param array $paymentData
     * @return Invoice
     * @throws ClientErrorException
     */
    public function processInvoicePayment(Invoice $invoice, float $amount, TransactionTypeEnum $transactionType): Invoice
    {
        if ($amount <= 0) {
            throw new ClientErrorException('Amount must be greater than zero', 400);
        }

        // Credit wallet in database transaction
        DB::transaction(function () use ($invoice, $amount, $transactionType) {
            $user = $invoice->user;
            if ($user->available_balance < $amount) {
                throw new ClientErrorException('Insufficient balance to pay the invoice', 400);
            }

            $newBalance = round($user->wallet_balance - $amount, 2);

            // Generate unique reference
            $reference = $this->generateTransactionRef();

            // Create wallet transaction record
            $transaction = WalletTransaction::create([
                'transaction_ref' => $reference,
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'action_type' => WalletTransaction::ACTION_TYPE_DEBIT,
                'transaction_type_id' => $transactionType->value,
                'description' => 'Invoice payment',
                'amount' => $amount,
                'wallet_balance_before' => $user->wallet_balance,
                'wallet_balance_after' => $newBalance,
                'status' => TransactionStatusEnum::SUCCESSFUL->value,
                'meta_data' => [],
            ]);

            // Update user wallet balance
            $user->update(['wallet_balance' => $newBalance]);

            // Update invoice status and amounts
            $invoice->applyPayment($amount);

            // Send notification to user
            if ($user->email) {
                SendNotificationJob::dispatch($user, new WalletTransactionNotification($transaction));
            }
        });

        return $invoice->fresh();
    }

    /**
     * Process invoice withheld payment
     *
     * @param WalletTransaction $transaction
     * @param array $paymentData
     * @return Invoice
     * @throws ClientErrorException
     */
    public function processInvoiceWithheldPayment(Invoice $invoice, float $amount): Invoice
    {
        if ($amount <= 0) {
            throw new ClientErrorException('Withheld amount must be greater than zero', 400);
        }

        // Credit wallet in database transaction
        DB::transaction(function () use ($invoice, $amount) {
            $user = $invoice->user;
            if ($user->withheld_balance < $amount) {
                throw new ClientErrorException('Insufficient withheld balance to pay the deposit', 400);
            }

            $newBalance = round($user->wallet_balance - $amount, 2);
            $newWithheldBalance = round($user->withheld_balance - $amount, 2);

            // Generate unique reference
            $reference = $this->generateTransactionRef();

            // Create wallet transaction record
            $transaction = WalletTransaction::create([
                'transaction_ref' => $reference,
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'action_type' => WalletTransaction::ACTION_TYPE_DEBIT,
                'transaction_type_id' => TransactionTypeEnum::PROPERTY_OFFER,
                'description' => 'Offer Deposit payment',
                'amount' => $amount,
                'wallet_balance_before' => $user->wallet_balance,
                'wallet_balance_after' => $newBalance,
                'status' => TransactionStatusEnum::SUCCESSFUL->value,
                'meta_data' => [],
            ]);

            // Update user wallet balance
            $user->update(['wallet_balance' => $newBalance]);
            $user->update(['withheld_balance' => $newWithheldBalance]);

            // Update invoice status and amounts
            $invoice->applyPayment($amount);

            // Send notification to user
            if ($user->email) {
                SendNotificationJob::dispatch($user, new WalletTransactionNotification($transaction));
            }
        });

        return $invoice->fresh();
    }

    public function processSellerCredit(string $invoiceId, User $user, float $amount): void
    {
        if ($amount <= 0) {
            throw new ClientErrorException('Amount must be greater than zero', 400);
        }

        // Credit wallet in database transaction
        DB::transaction(function () use ($invoiceId, $user, $amount) {
            $newBalance = round($user->wallet_balance + $amount, 2);

            // Generate unique reference
            $reference = $this->generateTransactionRef();

            // Create wallet transaction record
            $transaction = WalletTransaction::create([
                'transaction_ref' => $reference,
                'user_id' => $user->id,
                'invoice_id' => $invoiceId,
                'action_type' => WalletTransaction::ACTION_TYPE_CREDIT,
                'transaction_type_id' => TransactionTypeEnum::PROPERTY_PURCHASE->value,
                'description' => 'Payment from property sale',
                'amount' => $amount,
                'wallet_balance_before' => $user->wallet_balance,
                'wallet_balance_after' => $newBalance,
                'status' => TransactionStatusEnum::SUCCESSFUL->value,
                'meta_data' => [],
            ]);

            // Update user wallet balance
            $user->update(['wallet_balance' => $newBalance]);

            // Send notification to user
            if ($user->email) {
                SendNotificationJob::dispatch($user, new WalletTransactionNotification($transaction));
            }
        });
    }
}
