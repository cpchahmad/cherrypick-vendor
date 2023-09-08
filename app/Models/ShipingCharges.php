<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipingCharges extends Model
{
    use HasFactory;
    protected $table = 'shipping_charges';
    protected $guarded = []; 
}
