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

class UpdateProductsWeight implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 10000;
    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($id)
    {
        $this->id = $id;


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
            $product_type=ProductType::where('id',$this->id)->first();
            foreach ($products as $product){
                $variants=ProductInfo::where('product_id',$product->id)->get();
                foreach ($variants as $variant){

                    $pricing_weight=$variant->grams;

                    if($product_type && $product_type->base_weight){
                        $pricing_weight=max($variant->grams, $product_type->base_weight);
                    }

                    $variant->pricing_weight=$pricing_weight;
                    $variant->save();
                }


            }
        }catch (\Exception $exception){


        }

    }


}
