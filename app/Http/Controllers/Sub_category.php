<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\pending_sub_categories;
use App\Models\Sub_categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function League\Uri\validate;

class Sub_category extends Controller
{
    public function Get_pending_sub_category(Request $request)
    {
        try
        {
            $pendings = pending_sub_categories::all() ;

            foreach ($pendings as $pending)
            {
                $pending->catigory_name = Category::find($pending->category_id) ;
            }

            return  response()->json([
                'status' => true ,
                'pending_sub_category' => $pendings
                    ]) ;
        }catch (\Exception $ex)
        {
            return response()->json([
                'Status' => false,
                'Error' => $ex->getMessage()
            ], 500);
        }
    }
    public function Add_sub_category(Request $request): \Illuminate\Http\JsonResponse
    {

        try
        {
            $validate = Validator::make($request->all(), [
                'name' => 'required | string | min : 4' ,

                'category_id' => 'required '
            ]);

            if ($validate->fails())
            {
                return response()->json([
                    'Status' => false ,
                    'Validation Error ' => $validate->errors()
                ]) ;
            }

        }catch (\Throwable $Th)
        {

             return response()->json([
                'Status' => false,
                'Error In Create Sub_Category' => $Th->getMessage()
            ], 500);

        }
        try
        {
            //if not exist in pending_sub_categories or sub_categories
            $pending_sub_categories_Exist = pending_sub_categories::where('name' , $request['name'])->where('category_id' , $request['category_id']  ) ;
            $sub_categories_Exist = Sub_categories::where('name' , $request['name'])->where('category_id', $request['category_id']) ;

            if(! ($pending_sub_categories_Exist->first() or $sub_categories_Exist->first()))
            {
                if (Auth::user()->role == 'Admin')
                {
                    $sub_categories = Sub_categories::create([
                       'name' => $request['name'] ,

                       'category_id' => $request['category_id']
                    ]);

                    return response()->json([
                        'Status' => true ,
                        'sub_categories' =>$sub_categories
                    ]);

                }
                else
                {
                    $pending_sub_categories = pending_sub_categories::create([
                        'name' => $request['name'],

                        'category_id' => $request['category_id']
                    ]);
                    return response()->json([
                        'Status' => true ,
                        'sub_categories' =>$pending_sub_categories
                    ]);
                }
            }
            else
                return response()->json([
                    'Status' => false ,
                    'Message' => 'Sub_category is exist'
                ]);

        }
        catch (\Throwable $Th)
        {

            return response()->json([
                'Status' => false,
                'Error In Create Sub_Category' => $Th->getMessage()
            ], 500);

        }

    }

    public function Confirm_sub_category($pending_sub_categories_id): \Illuminate\Http\JsonResponse
    {
        if (Auth::user()->role == 'Admin')
        {

            try
            {
                $pending_sub_categories_Exist = pending_sub_categories::find($pending_sub_categories_id);

                if ($pending_sub_categories_Exist) {
                    $sub_categories = Sub_categories::create([
                        'name' => $pending_sub_categories_Exist->name,

                        'category_id' => $pending_sub_categories_Exist->category_id
                    ]);

                    $pending_sub_categories_Exist->delete();

                    return response()->json([
                        'Status' => true,
                        'Message' => 'Sub_Categories has been added successfully'
                    ]);

                }
            }
            catch (\Throwable $Th)
            {

                return response()->json([
                    'Status' => false,
                    'Error In Create Sub_Category' => $Th->getMessage()
                ], 500);

            }
        }else
        {
            return response()->json([
                'Status' => false ,
                'Message' => 'Invalid Request'
            ]);
        }

    }


    public function Deny_sub_category($pending_sub_categories_id): \Illuminate\Http\JsonResponse
    {
        try
        {
            $pending_sub_categories_Exist = pending_sub_categories::find($pending_sub_categories_id) ;

            if($pending_sub_categories_Exist)
            {
                $pending_sub_categories_Exist->delete() ;

                return response() ->json([
                    'Status' => true ,
                ]) ;

            }
        }
        catch (\Throwable $Th)
        {

            return response()->json([
                'Status' => false,
                'Error In Create Sub_Category' => $Th->getMessage()
            ], 500);

        }
    }
}
