<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Utils\Services\v1\Wallet\WalletService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletController extends Controller
{





  // protected WalletService $walletService;

  // public function __construct(WalletService $walletService)
  // {
  //   $this->walletService = $walletService;
  // }

  public function index()
  {
    return Inertia::render('wallet/index');
  }

  /**
   * Get wallet details
   */
  // public function getWalletDetails(Request $request)
  // {
  //   try {
  //     $walletDetails = $this->walletService->getWalletDetails();

  //     return Inertia::render('wallet/index', [
  //       'walletDetails' => $walletDetails
  //     ]);
  //   } catch (\Exception $e) {
  //     return back();
  //   }
  // }

  // /**
  //  * Initialize wallet funding
  //  */
  // public function initializeFunding(InitializeWalletFundingRequest $request)
  // {
  //   try {
  //     $paymentData = $this->walletService->initializeWalletFunding(
  //       $request->validated('amount'),
  //       $request->validated('email')
  //     );

  //     return back()->with('success', 'Wallet funding initialized successfully');
  //   } catch (\Exception $e) {
  //     return back()->with('error', $e->getMessage());
  //   }
  // }

  // /**
  //  * Verify payment after redirect
  //  */
  // public function verifyPayment(VerifyPaymentRequest $request): JsonResponse
  // {
  //   try {
  //     $result = $this->walletService->verifyPaymentAndCreditWallet(
  //       $request->validated('reference')
  //     );

  //     return $this->respondWithCustomData(
  //       message: $result['message'],
  //       data: $result
  //     );
  //   } catch (\Exception $e) {
  //     throw new ClientErrorException(
  //       message: $e->getMessage(),
  //       code: $e->getCode() ?: 400
  //     );
  //   }
  // }

  // /**
  //  * Get transaction history
  //  */
  // public function getTransactionHistory(Request $request): JsonResponse
  // {
  //   try {
  //     $request->validate([
  //       'per_page' => 'sometimes|integer|min:1|max:100',
  //       'search' => 'sometimes|string|max:255',
  //       'status' => 'sometimes|integer|in:1,2,3',
  //       'action_type' => 'sometimes|string|in:credit,debit',
  //       'min_amount' => 'sometimes|numeric|min:0',
  //       'max_amount' => 'sometimes|numeric|min:0',
  //       'start_date' => 'sometimes|date',
  //       'end_date' => 'sometimes|date|after_or_equal:start_date',
  //       'transaction_type_id' => 'sometimes|integer|exists:transaction_types,id',
  //       'sort' => 'sometimes|string|in:created_at,updated_at,amount,status,action_type',
  //       'direction' => 'sometimes|string|in:asc,desc',
  //     ]);

  //     $perPage = $request->get('per_page', 20);
  //     $transactions = $this->walletService->getTransactionHistory($perPage, $request);

  //     return $this->respondWithCustomData(
  //       message: 'Transaction history fetched successfully',
  //       data: $transactions
  //     );
  //   } catch (\Exception $e) {
  //     throw new ClientErrorException(
  //       message: $e->getMessage(),
  //       code: $e->getCode() ?: 400
  //     );
  //   }
  // }

  // /**
  //  * Get transaction details
  //  */
  // public function getTransactionDetails(Request $request, string $transactionId): JsonResponse
  // {
  //   try {
  //     $transaction = $this->walletService->getTransactionDetails($transactionId);

  //     return $this->respondWithCustomData(
  //       message: 'Transaction details fetched successfully',
  //       data: $transaction
  //     );
  //   } catch (\Exception $e) {
  //     throw new ClientErrorException(
  //       message: $e->getMessage(),
  //       code: $e->getCode() ?: 400
  //     );
  //   }
  // }

  // /**
  //  * Process payment webhook
  //  */
  // public function processWebhook(Request $request)
  // {
  //   try {
  //     $payload = $request->all();
  //     $signature = $request->header('X-Paystack-Signature');

  //     if (!$signature) {
  //      return back()->with('error', 'Invalid webhook signature');
  //     }

  //     $result = $this->walletService->processWebhook($payload, $signature);

  //     return $this->respondWithCustomData(
  //       message: $result['message'],
  //       data: $result
  //     );
  //   } catch (\Exception $e) {
  //     throw new ClientErrorException(
  //       message: $e->getMessage(),
  //       code: $e->getCode() ?: 400
  //     );
  //   }
  // }
}
