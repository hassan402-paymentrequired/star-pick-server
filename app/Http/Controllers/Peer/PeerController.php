<?php

namespace App\Http\Controllers\Peer;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PeerController extends Controller
{

    public function index()
    {
        return Inertia::render('peers/index');
    }
}
