<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInfo extends Model
{
    use HasFactory;
    protected $table = 'products_variants';

    protected $fillable = ['product_id','shipping_weight','sku','base_price','price_usd','price_aud','price_cad','price_gbp','price_nld','grams','stock','shelf_life','price','vendor_id','inventory_item_id','inventory_id','edit_status','new_add_status','price_status','inventory_status','product_discount','discounted_base_price','discounted_inr','discounted_usd','discounted_aud','discounted_cad','discounted_gbp','discounted_nld','price_conversion_update_status','varient_name','varient_value'];
//    public function product()
//     {
//      return $this->belongsTo(Product::class,'id');
//    }


    public function has_image(){
        return  $this->belongsTo('App\Models\ProductImages', 'product_id', 'product_id');
    }

}
