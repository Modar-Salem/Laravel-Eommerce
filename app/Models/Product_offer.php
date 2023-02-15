<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_offer extends Model
{
    use HasFactory;
    protected $table = 'product_offers'  ;

    protected $fillable = [
      'product_id' ,
      'offer_Id'
    ];
}
