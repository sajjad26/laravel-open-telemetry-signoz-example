<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\InteractsWithQueue;
use OpenTelemetry\API\Globals;

class DatabaseQueryListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(QueryExecuted $event): void
    {
        $tracerProvider = Globals::tracerProvider();
        $tracer = $tracerProvider->getTracer('web-endpoints-tracer');
        $span = $tracer->spanBuilder('database.query')->startSpan();
        $span->setAttribute('query', $event->sql);
        $span->setAttribute('duration', $event->time);
        $span->end();
    }
}
