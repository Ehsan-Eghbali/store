<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'parent_id'
    ];
    protected $hidden = ['pivot','deleted_at','created_at','updated_at'];
    public function categoryParent():HasOne
    {
        return $this->hasOne(Category::class,'id','parent_id')->with('categoryParent');
    }

}
