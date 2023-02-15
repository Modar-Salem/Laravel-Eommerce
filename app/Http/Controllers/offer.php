<?php

namespace App\Http\Controllers;

use App\Models\offers;
use App\Models\Product_offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class offer extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
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
            //validation
            $validate = Validator::make($request->all() ,[
                'product_id' => 'required ' ,
                'Discount' => 'integer | min :1 ' ,
            ] ) ;

            if ($validate->fails())
                return response()->json([
                    'Status'=> false ,
                    'Message' => $validate->errors()
                ]) ;

            $product = \App\Models\Product::find($request['product_id']) ;
            if ($product->user_id != Auth::id())
            {
                return response()->json([
                    'Status' => false ,
                    'Message' => 'Invalid Request'
                ]) ;
            }



            //Discount from product-price
            if ($request->has('offer_id')  )
            {
                $offer = offers::find($request['offer_id']) ;
                if (!$offer)
                {
                    return response()->json([
                        'Status' => false ,
                        'Message' => 'Product Not Found'
                    ]) ;
                }
                if (Auth::id() != $offer->user_id )
                    return response()->json([
                        'Status' => false ,
                        'Message' => 'This is offer Not to you'
                    ]);

                $product = \App\Models\Product::find($request['product_id']) ;

                if (!$product)
                    return response()->json([
                        'Status' => false ,
                        'Message' => 'Product Not Found'
                    ]) ;

                $product->price = $product->price - $offer->Discount;

                Product_offer::create([
                    'product_id' => $request['product_id'],
                    'offer_Id' => $request['offer_id']
                ]);

                return response()->json([
                    'Status' => true,
                    'Message' => 'Product has been added to offer successfully'
                ]);


            }
            elseif ($request ->has('Discount'))
            {

                $offer = offers::create([
                    'Discount' => $request['Discount'] ,
                    'user_id' =>Auth::id()
                ]) ;


                $product = \App\Models\Product::find($request['product_id']) ;
                if (!$product)
                    return response()->json([
                        'Status' => false ,
                        'Message' => 'Product Not Found'
                    ]) ;

                $product->price = $product->price - $offer->Discount ;
                $product->update() ;

                Product_offer::create([
                    'product_id' => $request['product_id'] ,
                    'offer_Id' => $offer->id,
                ])  ;

                return response()->json([
                    'Status' => true,
                    'Message' => 'offer has been created successfully And create the offer'
                ]);

            }
            else
            {
                return response()->json([
                    'Status' => false ,
                    'Message' => 'Invalid Request , You should add offer or Discount'
                ]) ;
            }



        }
        catch (\Exception $exception)
        {
            return response()->json([
                'Status' => false ,
                'Error In create Offer' => $exception->getMessage()
            ]) ;
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($offer_id)
    {
            $products_ex =  Product_offer::where('offer_id' , $offer_id) ;

            if (! $products_ex->first())
                return response()->json([
                    'Status' => false ,
                    'Message' => 'Not Found'
                ]) ;

            return response()->json([
            'Status' =>true ,
            'Product' => Product_offer::where('offer_id' , $offer_id) ->get()
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

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $offer_id)
    {
        try
        {
            $offer = offers::find($offer_id);
            $offer->update($request['Discount']);
            $offer->save();

            $Product_offer = Product_offer::where('offer_id', $offer_id);

            $Product_offer_ex = Product_offer::where('offer_id', $offer_id);

            if ($Product_offer_ex->first())
            {
                foreach ($Product_offer->get() as $product_offer) {
                    $product = \App\Models\Product::find($product_offer->id);
                    $product->update('price', $product->price + $offer->Discount - $request['Discount']);
                }
                return response()->json([
                    'Status' => true,
                    'Message' => 'Product Updated Successfully with new offer '
                ]);
            }else {
                return response()->json([
                    'Status' => false,
                    'Message' => 'this offer doesn\'t have product '
                ]);
            }
        }catch (\Exception $ex)
        {
                return response()->json([
                    'Status'=> false ,
                    'Error In Edit offer' => $ex->getMessage()
                ])  ;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($offer_id)
    {
        try
        {
            $offer = offers::find($offer_id);
            if (!$offer)
                return response()->json([
                    'Status' => false ,
                    'Message' => 'offer Not Found'
                ]) ;

            $offer_discount = $offer->Discount;

            $Product_offer_ex = Product_offer::where('offer_id', $offer_id);
            if ( $Product_offer_ex->first() )
            {
                $Product_offer = Product_offer::where('offer_id', $offer_id);

                foreach ($Product_offer->get() as $product_offer)
                {
                    $product = \App\Models\Product::find($product_offer->product_id);
                    $product->price = $product->price + $offer_discount;
                    $product->update();
                    $product_offer->delete() ;
                }
            }

            $offer->delete();


            return response()->json([
                'Status' => true,
                'Message' => 'offer has been deleted successfully'
            ]);

        }catch (\Throwable $th)
        {
                return response()->json([
                    'Status'  => false ,
                    'Error In Delete Offer' => $th->getMessage()
                ])    ;
        }
    }
}
