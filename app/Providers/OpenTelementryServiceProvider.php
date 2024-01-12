<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenTelemetry\API\Globals;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SDK\Sdk;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\TracerProviderBuilder;
use OpenTelemetry\SemConv\ResourceAttributes;

class OpenTelementryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->singleton(TracerProvider::class, function ($app) {
            $otlpTransportFactory = new OtlpHttpTransportFactory();
            $transport = $otlpTransportFactory->create('http://host.docker.internal:4318/v1/traces', 'application/json');

            $exporter = new SpanExporter($transport);

            $resource = ResourceInfoFactory::defaultResource()->merge(ResourceInfo::create(Attributes::create([
                ResourceAttributes::SERVICE_NAME => 'My PHP App',
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

            return $tracerProvider;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
