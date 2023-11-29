<?php

namespace App\Console\Commands;

use App\Models\Log;
use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Store;
use DB;
use Illuminate\Support\Carbon;

class ClearOldRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:old-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete records older than 7 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        \Illuminate\Support\Facades\DB::table('product_logs')
            ->where('created_at', '<', $sevenDaysAgo)
            ->delete();


    }
}
