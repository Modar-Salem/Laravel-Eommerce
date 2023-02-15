<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Favorite extends Controller
{

    public function Get_All_Favorite()
    {
        try
        {
            $products = DB::table('products')
                ->join('Favorite', 'products.id', '=', 'Favorite.product_id')
                ->get();

            return response()->json([
                $products
            ]) ;

        }
        catch (\Exception $exception)
        {
            return response()->json([
                'Status'=>false ,
                'Message'=>$exception->getMessage()
            ]) ;
        }
    }

    /**
     * Add the specified resource to favorite.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function Add_To_Favorite(int $product_id)
    {
        try
        {
            $user_id = Auth::id() ;

            $favorite = \App\Models\Favorite::where('user_id' , $user_id)->where('product_id' , '=' ,  $product_id);


            if ($favorite->first())
            {
                $favorite->delete() ;
                return response()->json([
                    'Status'=>true ,
                    'Message' => 'product un favorite'
                ]) ;

            }
            else
            {
                $product = \App\Models\Product::find($product_id) ;

                if($product) {
                    $user_id = Auth::id();
                    \App\Models\Favorite::create([
                        'product_id' => $product_id,
                        'user_id' => $user_id
                    ]);

                    return response()->json([
                        'Status' => true,
                        'Message' => 'product favorite'
                    ]);
                }
                else
                {
                    return response()->json([
                        'Status' => false ,
                        'Message' => 'Product Not Found'
                    ]) ;
                }
            }
        }
        catch (\Exception $exception)
        {
            return response()->json([
                'Status'=>false ,
                'Message'=>$exception->getMessage()
            ]) ;
        }
    }


    /**
     * Add the specified resource to favorite.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function Get_The_Favorite()
    {
        try
        {
            $user_id = Auth::id() ;
            $favorite_product = \App\Models\Favorite::where('user_id' , $user_id) ;

            return response()->json([
                'Status' => true ,
                'favorite_product' => $favorite_product->get()
            ]) ;

        }
        catch (\Exception $exception)
        {
            return response()->json([
                'Status'=>false ,
                'Message'=>$exception->getMessage()
            ]) ;
        }

    }
}
