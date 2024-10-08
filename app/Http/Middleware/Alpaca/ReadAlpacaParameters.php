<?php

namespace App\Http\Middleware\Alpaca;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class ReadAlpacaParameters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    
        // Get Parameters
        $bodyParams = $request->all();

        if(count($bodyParams) > 0){
            foreach($bodyParams as $key => $parameter){
                if(strtolower($key) == 'connected'){
                    $connect = false;
                    if(is_string($parameter) && strtolower($parameter) == 'true'){
                                $connect = true;
                    } elseif(is_int($parameter) && $parameter == 1){
                                $connect = true;
                    } elseif(is_bool($parameter) && $parameter){
                                $connect = true;
                    }

                    Session::put("connected",$connect);

                } else {
                    if(is_string($parameter)){
                        Session::put(strtolower($key),strtolower($parameter));
                    } else {
                        Session::put(strtolower($key),$parameter);
                    }   
                }
            }

        }

        return $next($request);
    }
}
