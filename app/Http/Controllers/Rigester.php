<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class Rigester extends Controller
{


    /**
     * SignUp And store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function SignUp(Request $request)
    {
        try
        {

                $validate = Validator::make($request->all() , [
                    'name' => 'required|string|max:255',

                    'email' => 'required|string|email|max:255|unique:users',

                    'password' => 'required|string|min:8|confirmed',

                    'phone' => 'required|string|min:10|max:20',
                ])  ;

                if ($validate->fails())
                    return response()->json([
                        'status' => false ,
                        'validation error' => $validate->errors()
                    ]) ;

                //create user AND create token

                $User = User::create([
                    'name' => $request['name'],

                    'email' => $request['email'],

                    'password' => \Illuminate\Support\Facades\Hash::make($request['password']),

                    'phone' => $request['phone']
                ]) ;

                $token = $User->createToken('API TOKEN')->plainTextToken ;
                //Success
                return response() -> json([
                    'status' => true ,
                    'user' => $User ,
                    'token' => $token
                ], 201) ;

        }catch (\Throwable $Th)
        {
                return response() -> json([
                    'status' => false  ,
                    'message' => $Th->getMessage() ,
                ] ,  500) ;
        }


    }

    /**
     * LogIn .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function LogIn(Request $request)
    {
         try
         {
            //Normal Email
            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials))
                return response()->json([
                    'status' => false ,
                    'message' => 'Invalid Data'
                ]);
            else
            {

                $User = \App\Models\User::where('email' , $request['email'])->first() ;
                $token = $User->createToken('API TOKEN')->plainTextToken ;

                return response() ->json([
                    'status'=> true ,
                    'user' => $User,
                    'token' => $token ,
                ]) ;
            }

         }
         catch (\Throwable $Th)
         {
             return response()->json([
                 'status' => false ,
                 'message' => $Th->getMessage()
             ],500) ;
         }
    }


    /**
     * Logout .
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function LogOut()
    {
        try
        {
            Auth::user()->tokens()->delete();

            return  response()->json([
                "status" => true ,
                "message" => "LogOut Successfully"
            ] ) ;

        }catch(\Exception $exception)
        {
            return response()->json([
                'status' => false ,
                'message' => $exception->getMessage()
            ]);
        }

    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try
        {

            $user = User::find($id) ;
            if ($user)
            {
                if ($id == Auth::id() or \auth()->user()->role = 'Admin') {
                    $user->update($request->all());
                    return response()->json([
                        'status' => true,
                        'message' => 'User has been Updated Successfully'
                    ]);
                }
                else
                    return response()->json([
                        'status' => false,
                        'message' => 'InValid Request'
                    ]);
            }
            else
            {
                return response()->json([
                    'status'=>false ,
                    'message' => 'User Not found'
                ]) ;
            }

        }
        catch (\Exception $exception)
        {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try
        {
                if ($id == Auth::id() or \auth()->user()->role = 'Admin')
                {
                        $user = User::find($id) ;

                        if ($user)
                        {
                                $user->delete();

                                $this->LogOut() ;

                                return response()->json([
                                    'status' => true,
                                    'message' => 'User has been deleted successfully'
                                ]);
                        }else
                        {
                            return response()->json([
                                'status' => false ,
                                'message' => 'User Not Found'
                                ]);
                        }

                }
        }
        catch (\Exception $exception)
        {
            return  response()->json([
                'status' => false ,
                'message' => $exception->getMessage()
            ]) ;
        }
    }
}
