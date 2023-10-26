<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'product_master';

 protected $fillable = ['shopify_id','title','handle','body_html','vendor','product_type','tags','status','is_variants','category','shopify_id','approve_date'];
   // protected $fillable = ['product_name','description','image','is_multi_varients','sku','price','weight','temp_requirements','compare_price','tags','shell_life','dimensions','quantity','category','vendor_id'];

   // public function productinfo()
     //{
     // return $this->hasMany(ProductInfo::class,'product_id');
    //}

    public function productInfo()
    {
        return $this->hasOne(ProductInfo::class);
    }
}
