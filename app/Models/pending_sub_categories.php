<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pending_sub_categories extends Model
{
    use HasFactory;
    protected $table = 'pending_sub_categories' ;

    protected $fillable = [
         'name' ,
         'category_id'
     ];

    public function catigory()
    {
        return $this->hasOne(Category::class) ;
    }
}
