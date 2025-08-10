<?php

namespace App\Http\Controllers\Profile;

use Inertia\Inertia;

class ProfileControlle extends \App\Http\Controllers\Controller
{

    public function index()
    {
        return Inertia::render('profile/index');
    }
}
