<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

 $app->withFacades();

 $app->withEloquent();
use Illuminate\Support\Facades\Log;
use Aws\Sns\Message;
config([
    'sns' => [
        // The credentials needed for the AWS client
        'client'        => [
            'id'      => env('AWS_ACCOUNT_ID'),
            'key'     => env('AWS_ACCESS_KEY'),
            'secret'  => env('AWS_SECRET_KEY'),
            'region'  => env('AWS_REGION'),
            'version' => 'latest',
        ],

        // The base URL for each of the routes, which is used to give SNS the right subscription endpoints
        'url'           => rtrim(env('APP_URL'), '/'),

        // Suitable defaults
        'defaults'      => [
            'topics' => [
                'region'  => env('AWS_REGION'),
                'id'      => env('AWS_ACCOUNT_ID'),
                'prefix'  => str_slug(env('APP_NAME')),
                'joiner'  => '_',
                'formARN' => function ($region, $id, $prefix, $joiner, $topic) {
                    // This default joiner will form ARNs similar to arn:aws:sns:us-east-2:1234567890:app-name_test-broadcast
                    $output = 'arn:aws:sns:' . $region . ':' . $id . ':';

                    if (!empty($prefix)) {
                        $output .= $prefix . $joiner;
                    }

                    return $output . $topic;
                },
            ],

            'subscriptions' => [
                // This will be the default route for all subscriptions
                'route' => '/sns',
            ],
        ],

        // The topics and their matching ARNs, which can be created with
        // php artisan sns:create
        'topics'        => [
            // The ARN can be formed using the defaults above
            'test-broadcast',

            // You can also define the ARN directly
            //'test-broadcast-arn' => 'arn:aws:sns:us-east-2:1234567890:test-broadcast-arn',
        ],

        // The topics to be subscribed to, and their matching actions
        'subscriptions' => [
            'test-broadcast' => [
                // 'controller' => 'BroadcastController@testBroadcast',
                // 'job'        => 'TestBroadcastJob',
                'callback' => function (Message $message) {
                    // Log::info('Broadcast received from ARN "' . $message->offsetGet('TopicArn') . '" with Message "' . $message->offsetGet('Message') . '".');
                },
            ],

            /*'test-broadcast-arn' => [
                // You can also dispatch an array of actions
                'controller' => [
                    'BroadcastController@testBroadcastARN',
                ],
                'job'        => [
                    'TestBroadcastARNJob',
                ],
                'callback'   => [
                    function (\Aws\Sns\Message $message) {
                        // Log::info('Broadcast received from ARN "' . $message->offsetGet('TopicArn') . '" with Message "' . $message->offsetGet('Message') . '".');
                    },
                ],
                // You can override the route on a per-subscription basis
                // 'route'      => '/sns/test-broadcast-arn',
            ],*/
        ],
    ],
]);

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);
$app->register(Mitchdav\SNS\LumenProvider::class);
$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//    App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

config([
    'broadcasting' => [

        'default' => env('BROADCAST_DRIVER', 'null'),

        'connections' => [

            'sns' => [
                'driver' => 'sns',
            ],

        ],

    ],
]);
$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
