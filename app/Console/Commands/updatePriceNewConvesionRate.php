<?php

namespace App\Console\Commands;

use App\Models\Log;
use Illuminate\Console\Command;
use App\Models\ProductInfo;
use Auth;
use DB;
use App\Helpers\Helpers;

class updatePriceNewConvesionRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:priceConversionRate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update product price';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		$data=ProductInfo::join('product_master', 'product_master.id', 'products_variants.product_id')
		->select('product_master.tags','products_variants.*')
		->where('price_conversion_update_status', 1)->orderBy('products_variants.id', 'DESC')->get();
        if(count($data) > 0) {

            $log=new Log();
            $log->name='Update Price Conversion';
            $log->date=date("F j, Y g:i a");
            $log->status='In-Progress';
            $log->save();
            try {
                foreach ($data as $row) {
                    $volumetric_Weight = 0;
                    $arr = explode("-", $row->dimensions);
                    if (is_numeric($arr[0]) && is_numeric($arr[1]) && is_numeric($arr[2]))
                        $volumetric_Weight = $arr[0] * $arr[1] * $arr[2] / 5000;
                    $prices = Helpers::calc_price_new($row->base_price, $row->grams, $row->tags, $volumetric_Weight, $row->vendor_id);
                    if ($prices) {
                        ProductInfo::where('id', $row->id)->update(['price_status' => 0, 'price_conversion_update_status' => 0, 'price' => $prices['inr'], 'price_usd' => $prices['usd'], 'price_aud' => $prices['aud'], 'price_cad' => $prices['cad'], 'price_gbp' => $prices['gbp'], 'price_nld' => $prices['nld']]);
                    }
                }
                $log->date=date("F j, Y g:i a");
                $log->status='Complete';
                $log->save();
            }catch (\Exception $exception){

                $log->date = date("F j, Y g:i a");
                $log->status = 'Failed';
                $log->message=json_encode($exception->getMessage());
                $log->save();
            }

        }

    }
}
