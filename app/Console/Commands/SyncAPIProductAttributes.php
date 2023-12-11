<?php

namespace App\Console\Commands;

use App\Http\Controllers\superadmin\SuperadminController;
use App\Models\Log;
use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Store;
use DB;
use Illuminate\Support\Carbon;

class SyncAPIProductAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:api-product-attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Kalamandir Product Attributes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $superAdminController=new SuperadminController();
        $superAdminController->SyncThirdPartyAPIAttributes();
    }
}
