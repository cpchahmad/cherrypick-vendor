<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\Setting;
use Illuminate\Console\Command;
use App\Models\ProductInfo;
use DB;

class updateInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update shopify inventory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $setting=Setting::first();
        if($setting){
            $API_KEY =$setting->api_key;
            $PASSWORD = $setting->password;
            $SHOP_URL =$setting->shop_url;

        }else{
            $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
            $PASSWORD = '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';
            $SHOP_URL = 'cityshop-company-store.myshopify.com';
        }

       $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/inventory_levels/set.json";
       //$product_inv = ProductInfo::where('stock', '>', 0)->whereNotNull('inventory_item_id')->get();
	   $product_inv = ProductInfo::where('inventory_status', 1)->whereNotNull('inventory_item_id')->get();
       if(count($product_inv) > 0) {
           $currentTime = now();
           $log = new Log();
           $log->name = 'Update Inventory';
           $log->date = $currentTime->format('F j, Y');
           $log->start_time = $currentTime->toTimeString();
           $log->status = 'In-Progress';
           $log->save();
           try {
               foreach ($product_inv as $row) {
                   $data = array(
                       'location_id' => '62600577199',
                       'inventory_item_id' => $row['inventory_item_id'],
                       'available' => $row['stock']
                   );
                   $curl = curl_init();
                   curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
                   $headers = array(
                       "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                       "Content-Type: application/json",
                       "charset: utf-8"
                   );
                   curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                   curl_setopt($curl, CURLOPT_VERBOSE, 0);
                   //curl_setopt($curl, CURLOPT_HEADER, 1);
                   curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                   //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                   curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                   curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                   curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                   $response = curl_exec($curl);
                   curl_close($curl);
                   ProductInfo::where('id', $row->id)->update(['inventory_status' => 0]);
               }
               $currentTime = now();
               $log->date = $currentTime->format('F j, Y');
               $log->end_time = $currentTime->toTimeString();
               $log->status = 'Complete';
               $log->save();
           }catch (\Exception $exception){
               $currentTime = now();
               $log->date = $currentTime->format('F j, Y');
               $log->status = 'Failed';
               $log->end_time = $currentTime->toTimeString();
               $log->message=json_encode($exception->getMessage());
               $log->save();
           }
       }
        //return Command::SUCCESS;
    }
}
