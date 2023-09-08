<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $data = session()->get('role');
        $vendorrole = session()->get('vendorrole');
        if($vendorrole=='Vendor'){
            return $next($request);
         }
         elseif($data->store_configuration==1){
            return $next($request);

         }
        else{
          return redirect()->route('home');

        }

    }
}
