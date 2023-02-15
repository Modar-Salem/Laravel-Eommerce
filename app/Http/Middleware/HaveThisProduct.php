<?php

namespace App\Http\Middleware;

use App\Models\Product;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HaveThisProduct
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $productId = Product::find($request->route('product_id'));

        if ($productId)
        {
            if (Auth::id() == $productId['user_id'] or Auth::user()->role == 'Admin')
                 return $next($request);

            else
                return response()->json( [
                    'Status'=> false ,
                    'Message' => 'Invalid Request'
                ]) ;
        }
        else
        {
                return response()->json([
                    'Status'=> false ,
                    'Message' => 'Product not found'
                ]) ;
        }
    }
}
