<?php

namespace App\Http\Middleware;

use App\Services\Alpaca\ServerTransitionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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

        if ($response instanceof \Illuminate\Http\JsonResponse){
            $data = $response->getData(true);

            if(Session::get('clientid')) {
                $data['clientID'] = Session::get('clientid');
            }

            if (Session::get('clienttransactionid')) {
                $data['ClientTransactionID'] = Session::get('clienttransactionid');
            }

            $serverTransition = ServerTransitionService::get();
            $data['ServerTransactionID'] = $serverTransition;
            $response->setData($data);

        }
        return $response;
    }
}
