<?php

namespace App\Jobs;

use App\Helpers\Helpers;
use App\Http\Controllers\superadmin\SuperadminController;
use App\Imports\BluckProductImport;
use App\Models\Category;
use App\Models\Log;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\ProductInfo;
use App\Models\ProductType;
use App\Models\Store;
use App\Models\VariantChange;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\SimpleExcel\SimpleExcelReader;

class UpdateProductPricesByProductType implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 10000;
    protected $id;
    protected $store;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($id,$store)
    {
        $this->id = $id;
        $this->store = $store;


    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        try {

            $products=Product::where('product_type_id',$this->id)->get();
            if(count($products) > 0 ) {
                $product_count=count($products);
                    $currentTime = now();
                    $log=new Log();
                    $log->name='Update Product Price in Database ('.$this->store.')';
                    $log->date = $currentTime->format('F j, Y');
                    $log->total_product = $product_count;
                    $log->start_time = $currentTime->toTimeString();
                    $log->status='In-Progress';
                    $log->save();

                foreach ($products as $product) {

                    $product_variants = ProductInfo::where('product_id', $product->id)->get();
                    foreach ($product_variants as $product_variant) {

                        $volumetric_Weight = 0;
                        $arr = explode("-", $product_variant->dimensions);
                        if (is_numeric($arr[0]) && is_numeric($arr[1]) && is_numeric($arr[2]))
                            $volumetric_Weight = $arr[0] * $arr[1] * $arr[2] / 5000;
                        $prices = Helpers::calc_price_new($product_variant->base_price, $product_variant->pricing_weight, $product->tags, $volumetric_Weight, $product_variant->vendor_id);

                        if ($prices) {
//                        ProductInfo::where('id', $row->id)->update(['price_status' => 0, 'price_conversion_update_status' => 0, 'price' => $prices['inr'], 'price_usd' => $prices['usd'], 'price_aud' => $prices['aud'], 'price_cad' => $prices['cad'], 'price_gbp' => $prices['gbp'], 'price_nld' => $prices['nld']]);
                            ProductInfo::where('id', $product_variant->id)->update(['price_conversion_update_status' => 0, 'price' => $prices['inr'], 'price_usd' => $prices['usd'], 'price_aud' => $prices['aud'], 'price_cad' => $prices['cad'], 'price_gbp' => $prices['gbp'], 'price_nld' => $prices['nld']]);
                        }


                    }
                }

                $currentTime = now();
                $log->date = $currentTime->format('F j, Y');
                $log->end_time = $currentTime->toTimeString();
                $log->status='Complete';
                $log->save();

            }

        }catch (\Exception $exception){
            $currentTime = now();
            $log->date = $currentTime->format('F j, Y');
            $log->status = 'Failed';
            $log->end_time = $currentTime->toTimeString();
            $log->message=json_encode($exception->getMessage());
            $log->save();

        }

    }


}
