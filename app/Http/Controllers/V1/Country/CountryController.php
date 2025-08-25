<?php

namespace App\Http\Controllers\V1\Country;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Support\Facades\Artisan;

class CountryController extends Controller
{

    public function index()
    {
        $countries = Country::all();

        return $this->respondWithCustomData(
            [
                'countries' => $countries
            ]
        );
    }

    public function refetch()
    {
        Artisan::call('fetch:countries');

        return $this->respondWithCustomData(
            [
                'message' => 'Countries refetched successfully'
            ]
        );
    }
}
