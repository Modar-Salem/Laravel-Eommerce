<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

// This is All Route of Register  :  SignUp  , LogIn  ,  LogOut

Route::prefix('Store')->group(function () {

    Route::Post('SignUp' , [\App\Http\Controllers\Rigester::class , 'SignUp'])->name('SignUp');

    Route::Post('LogIn' , [\App\Http\Controllers\Rigester::class , 'LogIn'])->name('LogIn')  ;

    Route::middleware('auth:sanctum')
        ->group(function ()
        {
            Route::get('LogOut' , [\App\Http\Controllers\Rigester::class , 'LogOut'])->name('LogOut') ;

            Route::put('Update/{id}' , [\App\Http\Controllers\Rigester::class , 'update' ])->name('Update') ;

            Route::delete('Delete/{id}' , [\App\Http\Controllers\Rigester::class , 'destroy'])->name('Delete') ;

        });

});
