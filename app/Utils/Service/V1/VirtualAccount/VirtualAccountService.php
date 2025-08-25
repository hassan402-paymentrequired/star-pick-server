<?php

namespace App\Utils\Service\V1\VirtualAccountService;

use App\Exceptions\ClientErrorException;
use App\Models\User;
use App\Models\VirtualAccount;
use App\Utils\Service\V1\Payment\PaystackService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VirtualAccountService
{
    protected PaystackService $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Get or create virtual account for user
     *
     * @param User $user
     * @return VirtualAccount
     * @throws ClientErrorException
     */
    public function getOrCreateVirtualAccount(User $user): VirtualAccount
    {
        try {
            // Check if user already has any virtual account (active or inactive)
            $existingAccount = $user->virtualAccount;



            if ($existingAccount) {
                // If account exists but is inactive, reactivate it
                if ($existingAccount->status !== 'active') {
                    $existingAccount->update(['status' => 'active']);
                    Log::info('VirtualAccountService: Reactivated existing virtual account', [
                        'user_id' => $user->id,
                        'account_id' => $existingAccount->id,
                    ]);
                }
                return $existingAccount;
            }

            // Create new virtual account only if user doesn't have any
            return $this->createVirtualAccount($user);

        } catch (\Exception $e) {
            Log::error('VirtualAccountService: Failed to get or create virtual account', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            throw new ClientErrorException('Failed to get virtual account: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create virtual account for user
     *
     * @param User $user
     * @return VirtualAccount
     * @throws ClientErrorException
     */
    public function createVirtualAccount(User $user): VirtualAccount
    {
        try {
            DB::beginTransaction();

            // Check if user already has any virtual account
            if ($user->hasVirtualAccount()) {
                throw new ClientErrorException('User already has a virtual account. Only one virtual account is allowed per user.', 400);
            }

            // Validate user has required information
            if (!$user->email || !$user->name) {
                throw new ClientErrorException('User must have email and name to create virtual account', 400);
            }

            // Create or get customer in Paystack
            $customerData = $this->paystackService->createOrGetCustomer($user);

            $defaultBankCode = config('paystack.default_bank_code');
            $defaultBankName = config('paystack.default_bank_name');

            // Create virtual account in Paystack
            $virtualAccountData = $this->paystackService->createVirtualAccount(
                $user,
                $customerData['customer_code'],
                $defaultBankCode,
                $user->name
            );

            // Create virtual account record in database
            $virtualAccount = VirtualAccount::create([
                'user_id' => $user->id,
                'account_number' => $virtualAccountData['account_number'],
                'account_name' => $virtualAccountData['account_name'],
                'bank_name' => $defaultBankName,
                'bank_code' => $defaultBankCode,
                'paystack_customer_code' => $customerData['customer_code'],
                'paystack_account_id' => $virtualAccountData['id'],
                'status' => 'active',
                'meta_data' => [
                    'paystack_data' => $virtualAccountData,
                    'customer_data' => $customerData,
                    'created_at' => now()->toISOString(),
                ],
            ]);

            DB::commit();

            Log::info('VirtualAccountService: Virtual account created successfully', [
                'user_id' => $user->id,
                'account_number' => $virtualAccount->account_number,
                'account_id' => $virtualAccount->paystack_account_id,
            ]);

            return $virtualAccount;

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('VirtualAccountService: Failed to create virtual account', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            throw new ClientErrorException('Failed to create virtual account: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deactivate virtual account
     *
     * @param VirtualAccount $virtualAccount
     * @return bool
     * @throws ClientErrorException
     */
    public function deactivateVirtualAccount(VirtualAccount $virtualAccount): bool
    {
        try {
            DB::beginTransaction();

            // Deactivate in Paystack
            if ($virtualAccount->paystack_account_id) {
                $this->paystackService->deactivateVirtualAccount($virtualAccount->paystack_account_id);
            }

            // Update status in database
            $virtualAccount->update([
                'status' => 'inactive',
                'meta_data' => array_merge($virtualAccount->meta_data ?? [], [
                    'deactivated_at' => now()->toISOString(),
                    'deactivation_reason' => 'User request',
                ]),
            ]);

            DB::commit();

            Log::info('VirtualAccountService: Virtual account deactivated successfully', [
                'account_id' => $virtualAccount->id,
                'account_number' => $virtualAccount->account_number,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('VirtualAccountService: Failed to deactivate virtual account', [
                'error' => $e->getMessage(),
                'account_id' => $virtualAccount->id,
            ]);
            throw new ClientErrorException('Failed to deactivate virtual account: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get virtual account transactions
     *
     * @param VirtualAccount $virtualAccount
     * @param int $perPage
     * @param int $page
     * @return array
     * @throws ClientErrorException
     */
    public function getVirtualAccountTransactions(VirtualAccount $virtualAccount, int $perPage = 50, int $page = 1): array
    {
        try {
            if (!$virtualAccount->paystack_account_id) {
                throw new ClientErrorException('Virtual account not properly configured', 400);
            }

            $transactions = $this->paystackService->getVirtualAccountTransactions(
                $virtualAccount->paystack_account_id,
                $perPage,
                $page
            );

            // Update last activity
            $virtualAccount->updateLastActivity();

            return $transactions;

        } catch (\Exception $e) {
            Log::error('VirtualAccountService: Failed to get virtual account transactions', [
                'error' => $e->getMessage(),
                'account_id' => $virtualAccount->id,
            ]);
            throw new ClientErrorException('Failed to get virtual account transactions: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get user's virtual account details
     *
     * @param User $user
     * @return array
     * @throws ClientErrorException
     */
    public function getUserVirtualAccountDetails(User $user): array
    {
        try {

            $virtualAccount = $this->getOrCreateVirtualAccount($user);

            return [
                'virtual_account' => $virtualAccount->accountDetails,
                'has_virtual_account' => true,
                'account_status' => $virtualAccount->status,
                'is_active' => $virtualAccount->isActive(),
            ];

        } catch (\Exception $e) {
            Log::error('VirtualAccountService: Failed to get user virtual account details', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            // Return empty details if virtual account creation fails
            return [
                'virtual_account' => null,
                'has_virtual_account' => false,
                'account_status' => 'unavailable',
                'is_active' => false,
                'error' => 'Virtual account temporarily unavailable',
            ];
        }
    }

    /**
     * Check if user has an existing virtual account
     *
     * @param User $user
     * @return bool
     */
    public function userHasVirtualAccount(User $user): bool
    {
        return $user->hasVirtualAccount();
    }

    /**
     * Get user's existing virtual account (if any)
     *
     * @param User $user
     * @return VirtualAccount|null
     */
    public function getUserVirtualAccount(User $user): ?VirtualAccount
    {
        return $user->virtualAccount;
    }

    /**
     * Process virtual account webhook
     *
     * @param array $payload
     * @param string $signature
     * @return array
     * @throws ClientErrorException
     */
    public function processVirtualAccountWebhook(array $payload, string $signature): array
    {
        try {
            // Verify webhook signature
            if (!$this->paystackService->verifyWebhookSignature(json_encode($payload), $signature)) {
                throw new ClientErrorException('Invalid webhook signature', 400);
            }

            $event = $payload['event'] ?? '';
            $data = $payload['data'] ?? [];

            switch ($event) {
                case 'dedicated_account.assigned':
                    return $this->processVirtualAccountAssigned($data);

                case 'dedicated_account.assigned_failed':
                    return $this->processVirtualAccountAssignmentFailed($data);

                case 'charge.success':
                    // Handle successful payment to virtual account
                    return $this->processVirtualAccountPayment($data);

                default:
                    return [
                        'message' => 'Virtual account webhook processed successfully',
                        'event' => $event,
                        'note' => 'Event not handled for virtual account operations'
                    ];
            }

        } catch (\Exception $e) {
            Log::error('VirtualAccountService: Virtual account webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            throw new ClientErrorException('Unable to process virtual account webhook: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process virtual account assigned event
     *
     * @param array $data
     * @return array
     */
    private function processVirtualAccountAssigned(array $data): array
    {
        Log::info('VirtualAccountService: Virtual account assigned', [
            'account_id' => $data['id'] ?? null,
            'account_number' => $data['account_number'] ?? null,
        ]);

        return [
            'message' => 'Virtual account assigned successfully',
            'account_number' => $data['account_number'] ?? null,
        ];
    }

    /**
     * Process virtual account assignment failed event
     *
     * @param array $data
     * @return array
     */
    private function processVirtualAccountAssignmentFailed(array $data): array
    {
        Log::error('VirtualAccountService: Virtual account assignment failed', [
            'data' => $data,
        ]);

        return [
            'message' => 'Virtual account assignment failed',
            'error' => $data['message'] ?? 'Unknown error',
        ];
    }

    /**
     * Process virtual account payment
     *
     * @param array $data
     * @return array
     */
    private function processVirtualAccountPayment(array $data): array
    {
        $accountNumber = $data['account_number'] ?? null;
        $amount = $data['amount'] ?? 0;
        $reference = $data['reference'] ?? null;

        Log::info('VirtualAccountService: Virtual account payment received', [
            'account_number' => $accountNumber,
            'amount' => $amount,
            'reference' => $reference,
        ]);

        // Find the virtual account
        $virtualAccount = VirtualAccount::where('account_number', $accountNumber)
            ->where('status', 'active')
            ->first();

        if (!$virtualAccount) {
            Log::warning('VirtualAccountService: Virtual account not found for payment', [
                'account_number' => $accountNumber,
                'reference' => $reference,
            ]);
            return [
                'message' => 'Virtual account not found',
                'account_number' => $accountNumber,
            ];
        }

        // Update last activity
        $virtualAccount->updateLastActivity();

        // TODO: Process the payment and credit user's wallet
        // This would involve creating a wallet transaction and updating user balance

        return [
            'message' => 'Virtual account payment processed successfully',
            'account_number' => $accountNumber,
            'amount' => $amount,
            'reference' => $reference,
        ];
    }
}

