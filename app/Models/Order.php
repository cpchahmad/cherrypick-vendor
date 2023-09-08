<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
	function ordersItems(){
		return $this->hasMany('App\Models\Orderitem','shopify_orders_id','shopify_order_id')->selectRaw('sum(price) as sum_amount');
	}
}
