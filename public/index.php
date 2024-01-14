<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SDK\Sdk;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SemConv\ResourceAttributes;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$otlpTransportFactory = new OtlpHttpTransportFactory();
$local = 'http://host.docker.internal:4318/v1/traces';
$hosted = 'https://ingest.us.signoz.cloud:443/v1/traces';
$transport = $otlpTransportFactory->create($hosted, 'application/json', [
    'Signoz-Access-Token' => 'b7ea9945-a7a5-4781-8cce-756195cd6120',
]);

$exporter = new SpanExporter($transport);

$resource = ResourceInfoFactory::defaultResource()->merge(ResourceInfo::create(Attributes::create([
    ResourceAttributes::SERVICE_NAME => 'AwesomeBooks',
])));

$tracerProvider = new TracerProvider(
    new SimpleSpanProcessor($exporter),
    new AlwaysOnSampler(),
    $resource,
);

Sdk::builder()
    ->setTracerProvider($tracerProvider)
    ->setAutoShutdown(true)
    ->buildAndRegisterGlobal();

$tracer = $tracerProvider->getTracer(
    'web-app-tracer'
);

$rootSpan = $tracer->spanBuilder('root')->startSpan();
$scope = $rootSpan->activate();

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);

$scope->detach();
$rootSpan->end();
