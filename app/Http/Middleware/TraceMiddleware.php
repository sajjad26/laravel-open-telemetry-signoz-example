<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TraceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $controllerName = get_class($request->route()->getController());
        $actionName = $request->route()->getActionMethod();
        $tracer = app('opentelemetry.tracer');
        $span = $tracer->spanBuilder("{$controllerName}@{$actionName}")->startSpan();
        $scope = $span->activate();
        $response = $next($request);
        $scope->detach();
        $span->end();
        return $response;
    }
}
