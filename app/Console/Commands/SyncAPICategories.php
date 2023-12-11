<?php

namespace App\Console\Commands;

use App\Http\Controllers\superadmin\SuperadminController;
use App\Models\Log;
use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Store;
use DB;
use Illuminate\Support\Carbon;

class SyncAPICategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:api-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Kalamandir Categories';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $superAdminController=new SuperadminController();
        $superAdminController->SyncThirdPartyAPICategories();
    }
}
