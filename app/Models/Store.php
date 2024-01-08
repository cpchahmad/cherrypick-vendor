<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $table = 'stores';
    protected $fillable = ['name','mobile','email','role','logo','description','status','password','profile_picture'];

// In your Store model
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'vendor', 'id');
    }



}
