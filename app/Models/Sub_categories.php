<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sub_categories extends Model
{
    use HasFactory;
    protected $table = 'sub_categories' ;

    protected $fillable = [
         'name' ,
         'category_id'
    ];

    public function catigory()
    {
        return $this->hasOne(Category::class) ;
    }
}
