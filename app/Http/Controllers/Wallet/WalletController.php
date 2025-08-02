<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class WalletController extends Controller
{

    public function index()
    {
      return Inertia::render('wallet/index');
    }
}
