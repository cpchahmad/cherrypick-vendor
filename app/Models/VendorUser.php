<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorUser extends Model
{
    use HasFactory;
    protected $fillable = ['first_name','last_name','phone','role','email','password','profile','vendor_id'];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
