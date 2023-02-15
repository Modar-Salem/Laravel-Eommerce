<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class offers extends Model
{
    use HasFactory;
    protected $table = 'offers' ;

    protected $fillable = [
        'Discount' ,
        'user_id'
    ] ;

    public function user()
    {
        return $this->hasOne(User::class) ;
    }
}
