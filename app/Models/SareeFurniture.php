<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SareeFurniture extends Model
{
    use HasFactory;
    protected $fillable = ['market_id','type','entered_price','converted_price'];
}
