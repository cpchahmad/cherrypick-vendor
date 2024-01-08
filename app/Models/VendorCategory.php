<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorCategory extends Model
{
    use HasFactory;

    public function childrenRecursive()
    {
        return $this->hasMany(VendorCategory::class, 'parent_id','id')->with('childrenRecursive');
    }

    protected static function booted()
    {
        static::deleting(function (VendorCategory $category) {
            $category->childrenRecursive()->delete(); // This will delete all child categories when a parent category is deleted.
        });
    }
}
