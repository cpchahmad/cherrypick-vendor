<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $table = 'banners';
    protected $fillable = ['home_desktop_banner','home_mobile_banner','store_desktop_banner','store_mobile_banner','vendor_id','store_slug','approve_status'];
}
