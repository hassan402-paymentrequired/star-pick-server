<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            ['name' => 'Access Bank',             'code' => '044'],
            ['name' => 'Zenith Bank',             'code' => '057'],
            ['name' => 'First Bank of Nigeria',   'code' => '011'],
            ['name' => 'United Bank for Africa',  'code' => '033'],
            ['name' => 'Guaranty Trust Bank',     'code' => '058'],
            ['name' => 'Ecobank Nigeria',         'code' => '050'],
            ['name' => 'Fidelity Bank',           'code' => '070'],
            ['name' => 'First City Monument Bank', 'code' => '214'],
            ['name' => 'Jaiz Bank',               'code' => '301'],
            ['name' => 'Keystone Bank',           'code' => '082'],
            ['name' => 'Sterling Bank',           'code' => '232'],
            ['name' => 'Union Bank of Nigeria',   'code' => '032'],
            ['name' => 'Polaris Bank',            'code' => '076'],
            ['name' => 'Providus Bank',           'code' => '101'],
            ['name' => 'Stanbic IBTC Bank',       'code' => '221'],
            ['name' => 'Standard Chartered Bank', 'code' => '068'],
            ['name' => 'Heritage Bank',           'code' => '030'],
            ['name' => 'TITAN Trust Bank',        'code' => '102'],
            ['name' => 'Suntrust Bank',           'code' => '100'],
        ];

        DB::table('banks')->insert($banks);
    }
}
