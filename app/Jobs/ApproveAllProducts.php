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

class ApproveAllProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 10000;
    protected $products;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($products)
    {
        $this->products = $products;


    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        try {



            $product_array_id=array();

            if(count($this->products) > 0){
                $currentTime = now();
                $log=new Log();
                $log->name='Approve Products';
                $log->date = $currentTime->format('F j, Y');
                $log->total_product = count($this->products);
                $log->start_time = $currentTime->toTimeString();
                $log->status='In-Progress';
                $log->save();

                foreach ($this->products as $product){

                    $product_info =ProductInfo::where('product_id',$product->id)->get();
                    $upload_product=0;
                    foreach($product_info as $index=> $v)
                    {
                        if($v->stock){
                            array_push($product_array_id,$product->id);
                        }
                    }

                }
                $product_array_id=array_unique($product_array_id);
                if(count($product_array_id) > 0) {
                    $data = Product::whereIn('id',$product_array_id)->update(['status'=>1,'approve_date' => Carbon::now()]);

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
