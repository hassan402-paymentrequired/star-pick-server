<?php

namespace App\Utils\Service\V1\Payment;

use App\Exceptions\ClientErrorException;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Utils\Enums\TransactionStatusEnum;
use App\Utils\Enums\TransactionTypeEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl;
    protected string $webhookSecret;
    protected $httpClient;

    public function __construct()
    {
        $this->baseUrl = config('paystack.base_url');
        $this->secretKey = config('paystack.secret_key');
        $this->publicKey = config('paystack.public_key');
        $this->webhookSecret = config('paystack.webhook_secret');

        if (!$this->secretKey || !$this->publicKey || !$this->baseUrl) {
            Log::error('PaystackService: Missing configuration', [
                'secret_key' => $this->secretKey ? 'set' : 'missing',
                'public_key' => $this->publicKey ? 'set' : 'missing',
                'base_url' => $this->baseUrl,
            ]);
            throw new ClientErrorException('Paystack API keys or base URL not configured', 500);
        }

        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ]
        ]);
    }

  


  

    /**
     * Verify a payment transaction
     *
     * @param string $reference
     * @return array
     * @throws ClientErrorException
     */
    public function verifyPayment(string $reference): array
    {
        try {
            $response = $this->httpClient->get("/transaction/verify/{$reference}");

            $data = json_decode($response->getBody()->getContents(), true);


            if (!$data['status'] || !isset($data['data'])) {
                throw new ClientErrorException('Failed to verify payment: ' . ($data['message'] ?? 'Unknown error'), 400);
            }

            return $data['data'];

        } catch (\Throwable $th) {
            Log::error('PaystackService: Payment verification failed', [
                'error' => $th->getMessage(),
                'reference' => $reference,
            ]);
            throw new ClientErrorException('Failed to verify payment: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create or get customer
     *
     * @param User $user
     * @return array
     * @throws ClientErrorException
     */
    public function createOrGetCustomer(User $user)
    {
        try {
            // First, try to find existing customer
            $response = $this->httpClient->get('/customer', [
                'query' => [
                    'email' => $user->email,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if ($data['status'] && isset($data['data']) && count($data['data']) > 0) {
                // Customer exists, return the first one
                return $data['data'][0];
            }

            // Customer doesn't exist, create new one
            $response = $this->httpClient->post('/customer', [
                'json' => [
                    'email' => $user->email,
                    'first_name' => $user->name,
                    'last_name' => $user->name,
                    'phone' => null, // Add if available
                    'metadata' => [
                        'user_id' => $user->id,
                        'user_type' => $user->user_type,
                    ]
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!$data['status'] || !isset($data['data'])) {
               return back()->with('error', 'Failed to create customer: ' . ($data['message'] ?? 'Unknown error'));
            }

            return $data['data'];

        } catch (\Throwable $th) {
            Log::error('PaystackService: Customer creation failed', [
                'error' => $th->getMessage(),
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return back()->with('error', 'Failed to create customer: ' . $th->getMessage());
            
        }
    }

    /**
     * Create virtual account
     *
     * @param User $user
     * @param string $customerCode
     * @param string $bankCode
     * @param string $accountName
     * @return array
     * @throws ClientErrorException
     */
    public function createVirtualAccount(User $user, string $customerCode, string $bankCode, string $accountName): array
    {
        try {
            $response = $this->httpClient->post('/dedicated_account', [
                'json' => [
                    'customer' => $customerCode,
                    'preferred_bank' => $bankCode,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!$data['status'] || !isset($data['data'])) {
                throw new ClientErrorException('Failed to create virtual account: ' . ($data['message'] ?? 'Unknown error'), 400);
            }

            return $data['data'];

        } catch (\Throwable $th) {
            Log::error('PaystackService: Virtual account creation failed', [
                'error' => $th->getMessage(),
                'user_id' => $user->id,
                'customer_code' => $customerCode,
                'bank_code' => $bankCode,
            ]);
            throw new ClientErrorException('Failed to create virtual account: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Deactivate virtual account
     *
     * @param string $accountId
     * @return array
     * @throws ClientErrorException
     */
    public function deactivateVirtualAccount(string $accountId): array
    {
        try {
            $response = $this->httpClient->post("/dedicated_account/deactivate", [
                'json' => [
                    'dedicated_account_id' => $accountId,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!$data['status']) {
                throw new ClientErrorException('Failed to deactivate virtual account: ' . ($data['message'] ?? 'Unknown error'), 400);
            }

            return $data['data'] ?? [];

        } catch (\Throwable $th) {
            Log::error('PaystackService: Virtual account deactivation failed', [
                'error' => $th->getMessage(),
                'account_id' => $accountId,
            ]);
            throw new ClientErrorException('Failed to deactivate virtual account: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get virtual account transactions
     *
     * @param string $accountId
     * @param int $perPage
     * @param int $page
     * @return array
     * @throws ClientErrorException
     */
    public function getVirtualAccountTransactions(string $accountId, int $perPage = 50, int $page = 1): array
    {
        try {
            $response = $this->httpClient->get("/dedicated_account/{$accountId}/transactions", [
                'query' => [
                    'perPage' => $perPage,
                    'page' => $page,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!$data['status']) {
                throw new ClientErrorException('Failed to get virtual account transactions: ' . ($data['message'] ?? 'Unknown error'), 400);
            }

            return $data['data'] ?? [];

        } catch (\Throwable $th) {
            Log::error('PaystackService: Failed to get virtual account transactions', [
                'error' => $th->getMessage(),
                'account_id' => $accountId,
            ]);
            throw new ClientErrorException('Failed to get virtual account transactions: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Verify webhook signature
     *
     * @param string $payload
     * @param string $signature
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        // If no webhook secret is configured (test mode), skip verification
        if (empty($this->webhookSecret)) {
            Log::warning('PaystackService: No webhook secret configured, skipping signature verification (test mode)');
            return true;
        }

        $computedSignature = hash_hmac('sha512', $payload, $this->webhookSecret);
        return hash_equals($computedSignature, $signature);
    }

    /**
     * Get list of Nigerian banks
     *
     * @return array
     * @throws ClientErrorException
     */
    public function getBanks(): array
    {
        try {
            $response = $this->httpClient->get('/bank');
            $data = json_decode($response->getBody()->getContents(), true);

            if (!$data['status'] || !isset($data['data'])) {
                throw new ClientErrorException('Failed to fetch banks: ' . ($data['message'] ?? 'Unknown error'), 400);
            }

            return $data['data'];

        } catch (\Throwable $th) {
            Log::error('PaystackService: Failed to fetch banks', [
                'error' => $th->getMessage(),
            ]);
            throw new ClientErrorException('Failed to fetch banks: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Verify bank account number
     *
     * @param string $accountNumber
     * @param string $bankCode
     * @return array
     * @throws ClientErrorException
     */
    public function verifyBankAccount(string $accountNumber, string $bankCode): array
    {
        try {
            $response = $this->httpClient->get("/bank/resolve", [
                'query' => [
                    'account_number' => $accountNumber,
                    'bank_code' => $bankCode,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!$data['status'] || !isset($data['data'])) {
                throw new ClientErrorException('Failed to verify account: ' . ($data['message'] ?? 'Unknown error'), 400);
            }

            return $data['data'];

        } catch (\Throwable $th) {
            Log::error('PaystackService: Bank account verification failed', [
                'error' => $th->getMessage(),
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);
            throw new ClientErrorException('Could not resolve account name. Check parameters or try again.', 500);
        }
    }

    /**
     * Initiate transfer to bank account
     *
     * @param string $recipientCode
     * @param float $amount
     * @param string $reason
     * @return array
     * @throws ClientErrorException
     */
    public function initiateTransfer(string $recipientCode, float $amount, string $reason): array
    {
        try {
            $response = $this->httpClient->post('/transfer', [
                'json' => [
                    'source' => 'balance',
                    'amount' => $amount * 100, // Convert to kobo
                    'recipient' => $recipientCode,
                    'reason' => $reason,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!$data['status'] || !isset($data['data'])) {
                throw new ClientErrorException('Failed to initiate transfer: ' . ($data['message'] ?? 'Unknown error'), 400);
            }

            return $data['data'];

        } catch (\Throwable $th) {
            Log::error('PaystackService: Transfer initiation failed', [
                'error' => $th->getMessage(),
                'recipient_code' => $recipientCode,
                'amount' => $amount,
            ]);
            throw new ClientErrorException('Failed to initiate transfer: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create transfer recipient
     *
     * @param string $type
     * @param string $name
     * @param string $accountNumber
     * @param string $bankCode
     * @return array
     * @throws ClientErrorException
     */
    public function createTransferRecipient(string $type, string $name, string $accountNumber, string $bankCode): array
    {
        try {
            $response = $this->httpClient->post('/transferrecipient', [
                'json' => [
                    'type' => $type,
                    'name' => $name,
                    'account_number' => $accountNumber,
                    'bank_code' => $bankCode,
                    'currency' => config('paystack.currency', 'NGN'),
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!$data['status'] || !isset($data['data'])) {
                throw new ClientErrorException('Failed to create recipient: ' . ($data['message'] ?? 'Unknown error'), 400);
            }

            return $data['data'];

        } catch (\Throwable $th) {
            Log::error('PaystackService: Recipient creation failed', [
                'error' => $th->getMessage(),
                'name' => $name,
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);
            throw new ClientErrorException('Failed to create recipient: ' . $th->getMessage(), 500);
        }
    }
}
