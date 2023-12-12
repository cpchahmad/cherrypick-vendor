<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyAPICategory extends Model
{
    use HasFactory;


    public function childrenRecursive()
    {
        return $this->hasMany(ThirdPartyAPICategory::class, 'parent_id','category_id')->with('childrenRecursive');
    }
}
