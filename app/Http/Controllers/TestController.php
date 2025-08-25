<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class TestController
{

    public function index()
    {
    $response = Http::get('https://www.sofascore.com/api/v1/sport/football/categories');

                if ($response->status() !== 403) {
                    return $response;
                }

    return $this->index();
    }
}
