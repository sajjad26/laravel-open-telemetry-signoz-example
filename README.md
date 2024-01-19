# Install OpenTelemetry

```bash
composer require open-telemetry/opentelemetry
```

I have already created some migrations and seeders to fill in some data for our sample AwesomeBooks application. So run the migrations and the seeds using the following command

```bash
php artisan migrate --seed
```

Now create `OpenTelementryServiceProvider`

```bash
php artisan make:provider OpenTelementryServiceProvider
```

Place the following code in the register function:

```php
public function register(): void
{
    $this->app->singleton('opentelemetry.tracer', function ($app) {
        $otlpTransportFactory = new OtlpHttpTransportFactory();
        $signozUrl = config('signoz.host') . '/v1/traces';
        $transport = $otlpTransportFactory->create($signozUrl, 'application/json', [
            'Signoz-Access-Token' => config('signoz.accessToken'),
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
```

The first singleton that we registered in the container is opentelemetry.tracer and it is responsible for creating a tracer for our application that we will use throughout the app.

The second singleton that we registered in the container is opentelemetry.rootSpan and it will act as the root span for all the traces and all other spans will be its children.

Now we want to start the rootSpan in application as early as possible and in order to do that we are going to create a middleware that will start the rootSpan for us. So Now, let's create a middleware named RootSpanMiddleware:

```bash
php artisan make:middleware RootSpanMiddleware
```

Place the following code in the handle method:

```php
public function handle(Request $request, Closure $next): Response
{
    app('opentelemetry.rootSpan');
    return $next($request);
}
```

Now we need to add this middleware to the application middlewares. To do this, let's modify the `app/Http/Kernel.php` file and add the new middleware in the protected $middleware array and at the very first index which will ensure this middleware executes before every other middleware:

```php
protected $middleware = [
    RootSpanMiddleware::class,
    \App\Http\Middleware\TrustProxies::class,
    // ... [Other middlewares]
];
```

Now that we have the rootSpan started we can think about instrumenting the actual parts of our application. For example, let's create another middleware called `TraceMiddleware` which will take care of instrumenting all the pages in the application.

```bash
php artisan make:middleware TraceMiddleware
```

Place the following code inside the handle method:

```php
public function handle(Request $request, Closure $next): Response
{
    $tracer = app('opentelemetry.tracer');
    $controllerName = get_class($request->route()->getController());
    $actionName = $request->route()->getActionMethod();
    $span = $tracer->spanBuilder("{$controllerName}@{$actionName}")->startSpan();
    $scope = $span->activate();
    $response = $next($request);
    $scope->detach();
    $span->end();
    return $response;
}
```

In this method we are creating a new span and activating it. We are also setting the name of the span to be the controller name joined with the method that is being called.
Note that it is very important to `detach` the activated spans and `end` the started spans. This will take care of instrumenting all our pages.

We can also instrument any unit of work using the same approach we did in the TraceMiddleware. For example, let's say we want to instrument all the database queries. We can create a listener named `DatabaseQueryListener`:

```bash
php artisan make:listener DatabaseQueryListener
```

Put the following code in the handle method:

```php
public function handle(QueryExecuted $event): void
{
    $tracer = app('opentelemetry.tracer');
    $span = $tracer->spanBuilder('database.query')->startSpan();
    $span->setAttribute('query', $event->sql);
    $span->setAttribute('bindings', $event->bindings);
    $span->setAttribute('time', $event->time);
    $span->end();
}
```

Here again we are creating a new span from the tracer we defined in our `OpenTelemetryServiceProvider`, we are also adding some useful information to the span like the actual query, the time it took and bindings.
Now add it to the App\Providers\EventServiceProvider:

```php
use Illuminate\Database\Events\QueryExecuted;
use App\Listeners\DatabaseQueryListener;

protected $listen = [
    ...
    QueryExecuted::class => [
        DatabaseQueryListener::class,
    ],
    ...
];
```

Let's say we have a page at the path /books/{id} and we want to add more context information to the traces.
We can easily do that with the following code in the controller `App\Http\Controllers\BooksController`

```php
use OpenTelemetry\API\Trace\Span;

public function book(int $id)
{
    $book = Book::with('authors')->find($id);
    Span::getCurrent()->setAttribute('book.id', $book->id);
    Span::getCurrent()->setAttribute('book.name', $book->name);
    return view('book', compact('book'));
}
```

Here we are accessing the currenty active span, in our case it will be the span we started in the `TraceMiddleware` and adding attributes to it like the book id and name. We could also start new spans here to instrument any sub logic in this method like if we were calling and external api, database or any complicated calculations.

We should also be able to know when and if our application was not working properly and throwing errors or exceptions. We can use the Laravel App\Exceptions\Handler class report method to do that:

```php
public function report(Throwable $e)
{
    $currentSpan = Span::getCurrent();
    $currentSpan->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
    $currentSpan->recordException($e);
}
```

So if an exception is raised it will also be recorded with the span.

Finally, when our application is shutting down, we need to end and detach the rootSpan. To do this, let's add this code to the OpenTelemetryServiceProvider boot method:

```php
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
```

If you have setup everything properly then you should browse the AwesomeBooks application by visiting http://localhost:8981/. Once you have visited a couple of different pages got to the SigNoz dashboard and open the Services page. You will see the `AwesomeBooks` service on the Services page in SigNoz dashboard.
![image](https://github.com/sajjad26/laravel-open-telemetry-signoz-example/assets/1017555/bdf0c0da-7d55-48ff-a56e-71c69e0e0fcd)

And you should see a couple of traces on the traces page as well in SigNoz
![image](https://github.com/sajjad26/laravel-open-telemetry-signoz-example/assets/1017555/f9ba2d2b-1126-49e1-a0fe-0b4c9ea81961)

This is how a single trace looks like
![image](https://github.com/sajjad26/laravel-open-telemetry-signoz-example/assets/1017555/ecc5c187-5e87-41e5-98c5-1270d69a5d28)


Happy coding and best of luck with your instrumentation endeavors in Laravel using OpenTelemetry and SigNoz. Adios!
