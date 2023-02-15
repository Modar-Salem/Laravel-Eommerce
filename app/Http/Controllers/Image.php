<?php

namespace App\Http\Controllers;

use Faker\Extension\Extension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class Image extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param   $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($image , int $product_id)
    {

            //validate Image
            try {
                $validate = Validator::make((array)$product_id,
                    [
                        'product_id' => 'foreignKeyExistsInProducts'
                    ]);
                if ($validate->fails())
                    return response()->json([
                        'Status' => false,
                        'Message' => $validate->errors()
                    ]);

            } catch (\Exception $exception) {
                return response()->json([
                    'Status' => false,
                    'Message' => $exception->getMessage()
                ]);
            }

            try
            {
                     $path = Null;

                    //Get FileName with extension
                    $filenameWithExt = $image->getClientOriginalName();

                    //Get FileName without Extension
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

                    //Get Extension
                    $Extension = $image->getClientOriginalExtension();

                    //New_File_Name
                    $NewfileName = $filename . '_' . time() . '_ .' . $Extension;

                    //Upload Image
                    $path = $image->storeAs('images', $NewfileName);


                    //create Object in Database
                    \App\Models\Image::create([
                        'path' => URL::asset('storage/' . $path),
                        'product_id' => $product_id
                    ]);


            } catch (\Exception $exception)
            {
                return response()->json([
                    'Status' => false,
                    'Message' => $exception->getMessage()
                ]);
            }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($product_id)
    {
        try
        {
            $images = \App\Models\Image::where('product_id' , $product_id) ;

            return response()->json([
                'Status'=> true ,
                'Message' => $images->get()
            ]) ;

        }catch (\Exception $exception)
        {
            return response()->json([
                'Status'=>false ,
                'Message' => $exception->getMessage()
            ]);
        }

    }

}
