<?php

namespace App\Http\Middleware;

use App\Services\Alpaca\ClientStatusService;
use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfClientIsConnected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $deviceType): Response
    {

        if(Session::has('clientid')){
            if(ClientStatusService::state(Session::get('clientid'),$deviceType)){
                return $next($request);
            }
        }

        return response()->json([
            'ErrorNumber' => 1031,
            'ErrorMessages' => 'Client Not Connected',
        ], 400);
    }
}
