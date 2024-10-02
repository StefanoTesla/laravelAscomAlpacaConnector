<?php

namespace App\Http\Middleware;

use App\Services\Alpaca\ServerTransitionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AscomAlpacaParameters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->ClientID && is_numeric($request->ClientID)) {
            $clientID= $request->ClientID;
        }

        
        if ($request->ClientTransactionID && is_numeric($request->ClientTransactionID)){
            $cliTransID = $request->ClientTransactionID;
        }

        if ($response instanceof \Illuminate\Http\JsonResponse && $clientID) {
            $data = $response->getData(true);
            $data['clientID'] = $clientID;
            $response->setData($data);
        }

        if ($response instanceof \Illuminate\Http\JsonResponse && $cliTransID) {
            $data = $response->getData(true);
            $data['ClientTransactionID'] = $cliTransID;
            $response->setData($data);
        }

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            $serverTransition = ServerTransitionService::get();
            $data['ServerTransactionID'] = $serverTransition;
            $response->setData($data);
        }        

        return $response;
    }
}
