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
        //validate from Request-Error
        try
        {

                $validate = Validator::make($request->all() , [
                    'name' => 'required | string | min:5 | max :34',

                    'phone' => 'required | string | numeric ',

                    'email' => 'required | email ',

                    'role' => 'boolean'


                ])  ;

                if ($validate->fails())
                    return response()->json([
                        'Status' => false ,
                        'Validation Error' => $validate->errors()
                    ],401) ;

        }
        catch (\Throwable $Th)
        {
                return response()->json([
                    'Status'=>false ,
                    'Error In Create User' => $Th->getMessage()
                ],500) ;
        }

        //create user AND create token
        try
        {
                $User = User::create([
                    'name' => $request['name'],

                    'email' => $request['email'],

                    'password' => \Illuminate\Support\Facades\Hash::make($request['password']),

                    'role' => 'Admin' ,

                    'phone' => $request['phone']
                ]) ;

                //create Token
                try
                {
                          $token = $User->createToken('API TOKEN')->plainTextToken ;
                }
                catch (\Throwable $Th)
                {
                         return response() -> json([
                                'Status' => false  ,
                                'Error in Create the Token' => $Th->getMessage() ,
                         ] ,  500) ;
                }


        }
        catch (\Throwable $Th)
        {
                return response() -> json([
                    'Status' => false  ,
                    'Error in Create the User' => $Th->getMessage() ,
                ] ,  500) ;
        }

        //Success
        return response() -> json([
            'Status' => true ,
            'User' => $User ,
            'Token' => $token
        ], 201) ;

    }

    /**
     * LogIn .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function LogIn(Request $request) {
         try
         {
            if (!Auth::attempt($request->only('email' , 'password' ))){
                    return response()->json([
                        'Status' => false ,
                        'Message' => 'Invalid Data'
                    ]);
            }
            else
            {

                $User = User::where('email' , $request['email'])->first() ;
                $token = $User->createToken('API TOKEN')->plainTextToken ;

                return response() ->json([
                    'Status'=> true ,
                    'Token' => $token ,
                ], 201) ;

            }

         }
         catch (\Throwable $Th)
         {
             return response()->json([
                 'Status' => false ,
                 'Message' => $Th->getMessage()
             ],500) ;
         }
    }

    /**
     * Logout .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function LogOut()
    {
            try
            {

                Auth::user()->tokens->each(function ($token){
                        $token->delete() ;
                        return response()->json([
                            'Status' => true ,
                            'Message' => 'LogOut Successfully'
                        ]) ;
                }) ;

            }
            catch(\Exception $exception)
            {
                    return response()->json([
                        'Status' => false ,
                        'Message' => $exception->getMessage()
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
                        'Status' => true,
                        'Message' => 'User has been Updated Successfully'
                    ]);
                }
                else
                    return response()->json([
                        'Status' => false,
                        'Message' => 'InValid Request'
                    ]);
            }
            else
            {
                return response()->json([
                    'Status'=>false ,
                    'Message' => 'User Not found'
                ]) ;
            }

        }
        catch (\Exception $exception)
        {
            return response()->json([
                'Status' => false,
                'Message' => $exception->getMessage()
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
                                    'Status' => true,
                                    'Message' => 'User has been deleted successfully'
                                ]);
                        }else
                        {
                            return response()->json([
                                'Status' => false ,
                                'Message' => 'User Not Found'
                                ]);
                        }

                }
                else
                {

                 }

        }
        catch (\Exception $exception)
        {
            return  response()->json([
                'Status' => false ,
                'Message' => $exception->getMessage()
            ]) ;
        }
    }
}
