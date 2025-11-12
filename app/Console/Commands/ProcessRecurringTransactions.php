<?php

namespace App\Console\Commands;

use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

class ProcessRecurringTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:process-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due recurring transactions and create them';

    /**
     * Define the application command schedule
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('transactions:process-recurring')->dailyAt('01:00');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing recurring transactions...');
        Log::info('Job [transactions:process-recurring] started.');

        $today = Carbon::today();
        $todayString = $today->toDateString();

        $dueTransactions = RecurringTransaction::where('is_active', true)->where('start_date', '<=', $todayString)->where('day_of_month', $today->day)
            ->where(function ($query) use ($today) {
                $query->whereNull('last_processed_at')
                      ->orWhere('last_processed_at', '<', $today->startOfMonth());
            })->get();

        if ($dueTransactions->isEmpty()) {
            $this->info('No due recurring transactions found.');
            Log::info('Job [transactions:process-recurring] finished: No transactions due.');
            return;
        }

        $createdCount = 0;

        foreach ($dueTransactions as $recurring) {

            Transaction::create([
                'user_id'           => $recurring->user_id,
                'category_id'       => $recurring->category_id,
                'amount'            => $recurring->amount,
                'description'       => $recurring->description,
                'transaction_date'  => $today,
            ]);

            $recurring->update(['last_processed_at' => $today]);

            $createdCount++;
        }

        $this->info("Successfully processed and created {$createdCount} transactions.");
        Log::info("Job [transactions:process-recurring] finished: Created {$createdCount} transactions.");
    }
}
