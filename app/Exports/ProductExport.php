<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Product;
use App\Models\ProductInfo;
use Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class ProductExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
     public function view(): View
    {
        if(Auth::user()->role=='Vendor')
           $vendor_id=Auth::user()->id;
       else
           $vendor_id=Auth::user()->vendor_id;
        $data=Product::select("product_master.title", "product_master.body_html", "product_master.tags",  "category.category", "products_variants.varient_name", "products_variants.varient_value", "products_variants.base_price", "products_variants.sku", "products_variants.grams", "products_variants.stock", "products_variants.shelf_life", "products_variants.temp_require", "products_variants.dimensions")
                ->leftJoin('category','product_master.category','category.id')
                ->join('products_variants','product_master.id','products_variants.product_id')
                ->where('product_master.vendor',$vendor_id)
                ->get();
				//echo "<pre>"; print_r($data); die;
        return view('expot',compact('data'));
    }
   
}
