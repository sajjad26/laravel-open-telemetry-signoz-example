<?php

namespace App\Http\Middleware;

use Attribute;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Content;
use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\Context\Context;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Trace\Tracer;
use Symfony\Component\HttpFoundation\Response;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SemConv\TraceAttributes;
use Throwable;

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
        $tracerProvider = Globals::tracerProvider();
        $parent = Context::getCurrent();
        $tracer = $tracerProvider->getTracer('web-endpoints-tracer');
        $parentSpan = Span::getCurrent();
        $parentSpan
            ->setAttribute(TraceAttributes::URL_PATH, $request->getRequestUri())
            ->setAttribute(TraceAttributes::HTTP_REQUEST_METHOD, $request->method())
            ->setAttribute(TraceAttributes::HTTP_METHOD, $request->method())
            ->updateName($request->getRequestUri());

        $span = $tracer->spanBuilder("{$controllerName}@{$actionName}")->startSpan();
        $scope = $span->activate();
        /** @var Response */
        $response = $next($request);
        $parentSpan->setAttribute(TraceAttributes::HTTP_RESPONSE_STATUS_CODE, $response->getStatusCode());
        $scope->detach();
        $span->end();
        return $response;
    }
}
