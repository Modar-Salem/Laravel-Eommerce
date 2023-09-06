<?php

use Illuminate\Support\Facades\Route;

//ALL Route have middleWare ' auth:sanctum '


Route::post('Add' , [\App\Http\Controllers\Product::class ,'store' ]) ;

Route::post('SearchProduct' , [\App\Http\Controllers\Product::class , 'show']) ;


Route::middleware('HaveThisProduct')->group(function (){


    Route::Get('GetMyProduct/{user_id}' , [\App\Http\Controllers\Product::class ,'index']  ) ;

    Route::put('EditMyProduct/{product_id}' , [\App\Http\Controllers\Product::class , 'update']) ;

    Route::delete('DeleteProduct/{product_id}' , [\App\Http\Controllers\Product::class , 'destroy']) ;

});


Route::prefix('Subcategory')->group(function(){

    Route::get('GetPending', [\App\Http\Controllers\Sub_category::class , 'Get_pending_sub_category']) ;

    Route::post('Add', [\App\Http\Controllers\Sub_category::class , 'Add_sub_category']) ;

    Route::post('Confirm/{pending_sub_categories_id}' , [\App\Http\Controllers\Sub_category::class , 'Confirm_sub_category']) ;

    Route::post('Deny/{pending_sub_categories_id}' , [\App\Http\Controllers\Sub_category::class , 'Deny_sub_category']) ;

});


Route::prefix('Rate') ->group(function (){

    Route::post('Give_Rate' , [\App\Http\Controllers\Product::class , 'rate']) ;

    Route::get('Get_Rate/{product_id}', [\App\Http\Controllers\Product::class ,'Get_Rate' ])  ;

});


Route::prefix('Favorite') ->group(function (){

    Route::post('Add/{product_id}' , [\App\Http\Controllers\Favorite::class , 'Add_To_Favorite']) ;

    Route::get('Get' , [\App\Http\Controllers\Favorite::class , 'Get_The_Favorite']) ;

    Route::get('Get_All_Favorite' , [\App\Http\Controllers\Favorite::class ,'Get_All_Favorite' ]) ;
});

Route::prefix('offer') -> group(function (){

    Route::post('Add' , [\App\Http\Controllers\offer::class ,'store']) ;

    Route::put('Update/{offer_id}' , [\App\Http\Controllers\offer::class ,'update']) ;

    Route::Get('Get/{offer_id}' , [\App\Http\Controllers\offer::class,  'show']) ;

    Route::delete('Delete/{offer_id}' , [\App\Http\Controllers\offer::class ,'destroy']) ;

});

Route::prefix('Purchase')->group(function (){

    Route::get('My_purchases', [\App\Http\Controllers\Purchase::class , 'index']) ;

    Route::get('Get_More_Popular' ,[\App\Http\Controllers\Purchase::class , 'Get_More_Popular'] ) ;

    Route::post('Buy' ,[\App\Http\Controllers\Purchase::class , 'store'] ) ;

});
