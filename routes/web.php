<?php

use Illuminate\Support\Facades\Route;
use OpenTelemetry\API\Globals;
use OpenTelemetry\SDK\Trace\TracerProvider;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    /** @var TracerProvider */
    $tracerProvider = app()->make(TracerProvider::class);
    $tracer = $tracerProvider->getTracer(
        'web-app-tracer'
    );
    $span = $tracer->spanBuilder('home-route-span')->startSpan();
    $number = rand();
    $view = view('welcome');
    $span->end();
    return $view;
});
