<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SemConv\ResourceAttributes;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\SemConv\TraceAttributes;

class OpenTelementryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('opentelemetry.tracer', function ($app) {
            $otlpTransportFactory = new OtlpHttpTransportFactory();
            $signozUrl = config('signoz.host') . '/v1/traces';
            $transport = $otlpTransportFactory->create($signozUrl, 'application/json', [
                'Signoz-Access-Token' => '56ef85a0-5378-4c66-aede-b82b6a2d7273',
            ]);

            $exporter = new SpanExporter($transport);

            $resource = ResourceInfoFactory::defaultResource()->merge(ResourceInfo::create(Attributes::create([
                ResourceAttributes::SERVICE_NAME => config('app.name'),
            ])));

            $tracerProvider = new TracerProvider(
                new SimpleSpanProcessor($exporter),
                new AlwaysOnSampler(),
                $resource,
            );

            return $tracerProvider->getTracer('awesome-books-tracer');
        });

        $this->app->singleton('opentelemetry.rootSpan', function ($app) {
            /** @var TracerInterface */
            $tracer = app('opentelemetry.tracer');
            $span = $tracer->spanBuilder(request()->getRequestUri())->startSpan();
            $span
                ->setAttribute(TraceAttributes::URL_PATH, request()->getRequestUri())
                ->setAttribute(TraceAttributes::HTTP_REQUEST_METHOD, request()->method())
                ->setAttribute(TraceAttributes::HTTP_METHOD, request()->method());
            $scope = $span->activate();

            return [$span, $scope];
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->terminating(function () {
            [$rootSpan, $rootScope] = app('opentelemetry.rootSpan');
            if ($rootSpan) {
                $rootScope->detach();
                $rootSpan->end();
            }
        });
    }
}
