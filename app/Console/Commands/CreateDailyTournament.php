<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateDailyTournament extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:tournament {name?} {amount?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        
        $existingContest = \App\Models\DailyContest::whereDate('created_at', $today)->first();
        
        if (!$existingContest) {
            \App\Models\DailyContest::create([
                'name' => $this->argument('name') ?? now()->format('l') . ' Tournament',
                'amount' => $this->argument('amount') ?? 0.00,
            ]);
            $this->info('Daily contest created for today.');
        } else {
            $this->info('A daily contest already exists for today.');
        }
    }
}
