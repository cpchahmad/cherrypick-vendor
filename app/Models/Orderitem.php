<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderitem extends Model
{
    use HasFactory;
    protected $table = 'order_items';


    public function has_variant(){
        return  $this->belongsTo('App\Models\ProductInfo', 'shopify_variant_id', 'inventory_id');
    }
}
