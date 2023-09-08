<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model
{
    use HasFactory;
    protected $table = 'products_images';
	protected $guarded = [];  
   // protected $fillable = ['product_name','description','image','is_multi_varients','sku','price','weight','temp_requirements','compare_price','tags','shell_life','dimensions','quantity','category','vendor_id'];

   // public function productinfo()
     //{
     // return $this->hasMany(ProductInfo::class,'product_id');
    //}
}
