<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\InteractsWithQueue;

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
        $tracer = app('opentelemetry.tracer');
        $span = $tracer->spanBuilder('database.query')->startSpan();
        $span->setAttribute('query', $event->sql);
        $span->setAttribute('bindings', $event->bindings);
        $span->setAttribute('time', $event->time);
        $span->end();
    }
}
