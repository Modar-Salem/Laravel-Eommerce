<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected  $table = 'rates' ;

    protected $fillable = [
        'Rate' ,

        'user_id' ,

        'product_id'
    ] ;

    use HasFactory;
}
