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
    protected $log_id;


    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($id,$log_id)
    {
        $this->id = $id;
        $this->log_id = $log_id;


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
            $product_ids=Product::where('product_type_id',$this->id)->pluck('id')->toArray();
            $product_type=ProductType::where('id',$this->id)->first();
            $currentTime = now();
            if(count($products) > 0){
                $product_count=count($products);
                $log=Log::where('id',$this->log_id)->first();
                if($log) {
                    $log->total_product = $product_count;
                    $log->status = 'In-Progress';
                    $log->product_ids = implode(',', $product_ids);
                    $log->save();
                }
            foreach ($products as $product){
                $variants=ProductInfo::where('product_id',$product->id)->where('manual_weight',0)->get();
                foreach ($variants as $variant){


                    $pricing_weight=$variant->grams;

                    if($product_type && $product_type->base_weight){
                        $pricing_weight=max($variant->grams, $product_type->base_weight);
                    }

                    $variant->pricing_weight=$pricing_weight;
                    $variant->save();
                }


            }

                $currentTime = now();
                    if($log) {
                        $log->end_time = $currentTime->toTimeString();
                        $log->status = 'Complete';
                        $log->save();
                    }
            }else{
                $log=Log::where('id',$this->log_id)->first();
                if($log) {
                    $log->end_time = $currentTime->toTimeString();
                    $log->status = 'Complete';
                    $log->save();
                }

            }
        }catch (\Exception $exception){
            $log=Log::where('id',$this->log_id)->first();
            $currentTime = now();
            $log->date = $currentTime->format('F j, Y');
            $log->status = 'Failed';
            $log->end_time = $currentTime->toTimeString();
            $log->message=json_encode($exception->getMessage());
            $log->save();
        }

    }


}
