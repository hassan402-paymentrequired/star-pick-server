<?php

namespace App\Http\Controllers\V1\Payment;

use App\Http\Controllers\Controller;
use App\Utils\Service\V1\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function initialize(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:100',
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
            'Cache-Control' => 'no-cache',
        ])->post('https://api.paystack.co/transaction/initialize', [
            'email' => $request->email,
            'amount' => $request->amount, 
            'callback_url' => route('paystack.callback'),
            'metadata' => [
                'cancel_action' => route('paystack.cancel'),
            ]
        ]);

        Log::info('Paystack Initialize Response', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        return $this->respondWithCustomData([
            'authorization_url' => $response->json()['data']['authorization_url'],
            'message' => 'Payment initialized successfully'
        ], 200);
    }
}
