<?php

namespace App\Http\Controllers;

use App\Models\Product_offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Purchase extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
         try
         {
             $user_id =Auth::id() ;
             $products = DB::table('products')
                ->join('purchases', 'products.id', '=', 'purchases.product_id' )->where('purchases.user_id' , $user_id) ; ;

             $products_ex =  DB::table('products')
                ->join('purchases', 'products.id', '=', 'purchases.product_id' )->where('purchases.user_id' , $user_id) ;

             if ($products_ex->first())
             {
                return response()->json([
                    'Status' => true,
                    'Products' => $products->get(),
                    'The Total Price' => $products->sum('price')
                ]);

             }

            else {
                return response()->json([
                    'Status' => false,
                    'Message' => 'You are haven\'t any purchase'
                ]);
            }

         } catch (\Exception $exception)
         {
             return response()->json([
                 'Status' => false ,
                 'Message' => $exception->getMessage()
             ]) ;
         }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        try
        {
            $validate = Validator::make($request->all() ,
            [
                    'product_id'=> 'required' ,
                    'amount' => 'required'
            ]) ;
            if ($validate->fails())
            {
                    return response()->json([
                        'Status' => false ,
                        'Error In Validate' => $validate->errors()
                    ]) ;
            }

            $product = \App\Models\Product::find($request['product_id']);

            if (!$product)
            {
                return response()->json(
                    [
                        'Status' => false ,
                        'Message' => 'Product Not Found'
                    ]);
            }

            $user_id = Auth::id();

            if($request['amount']>$product->amount)
            {
                return response()->json([
                    'Status'=>  false ,
                    'Message' => 'This Amount doesn\'t available'
                ]) ;
            }

            \App\Models\Purchase::create([
                'product_id' => $request['product_id'],
                'user_id' => $user_id ,
                'amount' => $request['amount']
            ]);

            $product->amount = $product->amount - $request['amount'] ;
            $product->update() ;

            if($product->amount == 0 )
            {
                $Favorite_ex = \App\Models\Favorite::where('product_id' , $product->id) ;
                if($Favorite_ex->first())
                    \App\Models\Favorite::where('product_id' , $product->id)->delete() ;

                $product_image = \App\Models\Image::where('product_id' , $product->id) ;
                if($product_image->first())
                    \App\Models\Image::where('product_id' , $product->id)->delete() ;

                $product_offer = Product_offer::where('product_id' , $product->id) ;
                if($product_offer->first())
                    Product_offer::where('product_id' , $product->id)->delete() ;

                $product_purchase = \App\Models\Purchase::

                $product->delete() ;
            }
            return response()->json([
                'Status' => true ,
                'Message' => 'Purchase completed successfully'
            ]) ;


        }catch (\Exception $exception)
        {
            return response()->json([
                'Status' => false ,
                'Message' => $exception->getMessage()
            ]) ;
        }

    }


    /**
     * Display the Most_Popular_Group resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function Get_More_Popular()
    {
        // select : is select a column
        //raw(count(product_id) as how_much) : return The number of field_repetitions and stored it in how_much (this will execute for every row) ,
        //groupBy('product_id') : will group all row that have same product_id in one row
        //orderBy('how_much','desc') : sort descending
        $More_Popular = \App\Models\Purchase::select('product_id' , \App\Models\Purchase::raw('count(product_id) as how_much'))
            ->groupBy('product_id')->orderBy('how_much','desc')->limit(3)->get() ;

        //create array
        $More_Popular_product  = array();

        foreach ($More_Popular as $more_popular)
        {
            $More_Popular_product[] = \App\Models\Product::find($more_popular['product_id']) ;
        }

        return response()->json([
            'Status' => true ,
            'Message' => $More_Popular_product
        ]) ;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
