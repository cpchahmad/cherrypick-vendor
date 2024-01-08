<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\ProductLog;
use App\Models\ProductType;
use App\Models\Setting;
use App\Models\VendorUrl;
use Illuminate\Console\Command;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductInfo;
use App\Models\ProductImages;
use App\Models\ProductInventoryLocation;
use Auth;
use App\Models\OrdersOtp;
use App\Helpers\Helpers;
use DB;
use Collection;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FetchProductShopifyUrlMainCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:product-shopifyurl-main';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store Vendor Url';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $vendor_data = DB::table('cron_json_url')->get();
        if (count($vendor_data) > 0) {

            $currentTime = now();
            $log = new Log();
            $log->name = 'Fetch Products';
            $log->date = $currentTime->format('F j, Y');
            $log->start_time = $currentTime;
            $log->status = 'In-Progress';
            $log->save();

            foreach ($vendor_data as $val) {

                try {
                    $vid = $val->vendor_id;
                    $store = Store::find($vid);
                    if ($store && $store->status == 'Active') {
                        $url = $val->url;

                        $vendor_url=new VendorUrl();
                        $vendor_url->vendor_id=$val->vendor_id;
                        $vendor_url->vendor_name=$store->name;
                        $vendor_url->status='In-Queue';
                        $vendor_url->log_id=$log->id;
                        $vendor_url->save();

                    }
                }catch (\Exception $exception){


                }
                }
        }

    }

}
