<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenTelemetry\SDK\Trace\TracerProvider;

class HomeController extends Controller
{
    public function index()
    {
        /** @var TracerProvider */
        $tracerProvider = app()->make(TracerProvider::class);
        $tracer = $tracerProvider->getTracer(
            'web-app-tracer'
        );
        $span = $tracer->spanBuilder('home-route-span')->startSpan();
        $number = rand();
        $span->end();
        return view('home');
    }
}
