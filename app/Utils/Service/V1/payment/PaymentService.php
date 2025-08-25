<?php

namespace App\Utils\Service\V1\Payment;




use App\Exceptions\ClientErrorException;
use Illuminate\Support\Facades\Log;

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

   
    public function verifyBankAccount(string $accountNumber, string $bankCode)
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
                return false;
            }

            return $data['data'];

        } catch (\Throwable $th) {
            Log::error('PaystackService: Bank account verification failed', [
                'error' => $th->getMessage(),
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);
            dd($th->getMessage());
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
