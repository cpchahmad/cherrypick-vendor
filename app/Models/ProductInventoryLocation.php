<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInventoryLocation extends Model
{
    use HasFactory;
    protected $table = 'products_inventory_items_stock';
    protected $guarded = []; 
    public $timestamps = false;
}
