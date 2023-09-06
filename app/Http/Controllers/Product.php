<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Image ;
use App\Models\Favorite;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Type\Integer;

class Product extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($user_id)
    {
        try
        {

                $user = User::find($user_id) ;

                if ($user)
                {
                        $products = \App\Models\Product::where('user_id', $user_id);

                        if ($products->first())
                        {
                            return response()->json([
                                'Status' => true ,
                                'Products' => \App\Models\Product::where('user_id', $user_id) ->get()
                            ]) ;
                        }

                        else
                        {
                            return response()->json([
                                'Status' => false ,
                                'Products' => 'This  User haven\'t Product  '
                            ]) ;
                        }
                }
                else
                {
                    return response()->json([
                        'Status' => false ,
                        'Message' => "User Not Exist"
                    ]) ;
                }

        }
        catch (\Exception $exception)
        {
                return response() ->json( [
                    'Status' => false ,
                    'Message' => $exception->getMessage()
                ],401)  ;

        }


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
            //Validate
            try
            {

                $validate = Validator::make($request->all(), [
                    'name' => 'required | string | min:5 |max : 34 ',

                    'amount' => 'required | integer | min : 1 ',

                    'price' => 'required | integer ',

                    'details' => 'string ',

                    'subcategory_id' => 'exists:sub_categories,id' ,

                    'category_id' =>'exists:categories,id'

                ]);
            }
            catch (\Exception $exception ) {
                return response()->json([
                        'Status' => false ,
                        'Message' => $exception->getMessage()
                ], 401 ) ;
            }
            // create product

            try
            {

                    $id = Auth::id();
                    $product = \App\Models\Product::create([
                        'name' => $request['name'] ,

                        'amount' => $request['amount'] ,

                        'price' => $request ['price'],

                        'details' =>$request ['details'] ,

                        'user_id' => $id ,

                        'category_id' => $request ['category_id'] ,

                        'subcategory_id' => $request ['subcategory_id'] ,

                    ]) ;

                    if ($request->hasFile('image'))
                    {
                        (new Image)->store($request->file('image') , $product->id) ;
                    }

                    if ($request->hasFile('image1'))
                    {
                        (new Image)->store($request->file('image1'), $product->id);
                    }

                    if ($request->hasFile('image2'))
                    {
                        (new Image)->store($request->file('image2'), $product->id);
                    }

                    if ($request->hasFile('image3'))
                    {
                        (new Image)->store($request->file('image3'), $product->id);
                    }


                return response() ->json([
                        'Status' => true ,
                        'Product'=> $product
                    ],201) ;

            }
            catch (\Exception $exception)
            {
                return response()->json([
                        'Status' => false ,
                        'Message'=> $exception->getMessage()
                ]) ;
            }

        }
        catch (\Exception $exception)
        {
            return response()->json([
                'Status' => true ,
                'Message' => $exception->getMessage()
            ], 401) ;
        }

    }


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        try
        {

            $product = \App\Models\Product::query() ;
            if ($request['name'] != null)
                    $product = $product->where('name' ,'like' ,'%'.$request['name'].'%')->orWhere('details' ,'like' ,'%'.$request['name'].'%') ;

            if ($request['price']!=null)
                    $product = $product->where('price' , '>' , $request['price']) ;

            if($request['category_id']!=null)
            {
                $product = $product->where('category_id' , $request['category_id']) ;

            }
            if ($request['subcategory_id']!=null)
                    $product = $product->where('subcategory_id' , $request['subcategory_id']) ;

            if($product)
                return response()->json([
                    'Status'=>true ,
                    'Products'=> $product->get()
                ],201) ;

        }
        catch (\Exception $exception){

            return response()->json([
                'Status' => false ,
                'Message' => $exception->getMessage()
            ], 401) ;
        }


    }


    /**
     * give a rate to the product
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rate(Request $request)
    {
        try
        {
                $validate = Validator::make($request->all() ,
                [
                    'Rate' => 'Required | min :1 | max :5 |integer ',
                ]) ;

                if($validate->fails())
                {
                    return response()->json([
                        'Status' => false ,
                        'Message' => $validate->errors()
                    ]) ;
                }

                $user_id = Auth::id() ;
                Rate::updateOrCreate([
                    'user_id'=> $user_id
                ],
                [
                    'Rate' => $request['Rate'] ,
                    'product_id' => $request['product_id']
                ]) ;

                return response()->json([
                    'status' => true ,
                    'Message' => 'Rated Successfully'
                ]) ;


        }
        catch (\Exception $exception)
        {
               return response()->json([
                   'Status' => false ,
                   'Message' => $exception->getMessage()
               ], 401) ;
        }


    }


    /**
     * give a rate to the product
     *
     * @param int $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function Get_Rate(int $product_id){
        try
        {
            $count = Rate::where('product_id' , '=', $product_id ) -> count() ;
            $sum = Rate::where('product_id' , '=', $product_id )-> sum('Rate') ;

            return response()->json([
                'Status' => true ,
                'Rate' => $sum/$count
            ]) ;


        }catch (\Exception $exception)
        {
            return response()->json([
                'Status' => false ,
                'Message' => $exception->getMessage()
            ], 401) ;

        }
    }



    public function update(Request $request, $id)
    {
            $product = \App\Models\Product::find($id) ;

            if ($product)
            {
                $product->update($request->all()) ;

                $product -> save() ;

                return response()->json([
                     'Status' => true ,
                     'Message'=> 'Product has been updated successfully ']) ;
            }
            else
                return response()->json([
                    'Status' => true ,
                    'Message' => ['ID NOT FOUND']
                ]) ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($product_id)
    {
        try
        {
            $product = \App\Models\Product::find($product_id) ;

            if ($product)
            {

                    $product_image = \App\Models\Image::where('product_id' , $product_id) ;
                    $product_image->delete() ;

                    $product->delete() ;

                    return response()->json([
                        'Status' => true ,
                        'Message' => 'Record has been Deleted Successfully'
                    ]) ;

            }
            else
            {
                return response()->json([
                    'Status' => false ,
                    'Message' => 'Record Isn\'t exist '
                ]) ;
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




}
