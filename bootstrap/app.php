<?php

use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\OAuthServerException;
// use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    // ->withExceptions(function (Exceptions $exceptions) {
    //     // Check if the request expects a JSON response (API request)
    //     $exceptions->report(function ($request, AuthenticationException $e) {
    //         if ($e instanceof AuthenticationException) {
    //             if ($request->expectsJson()) {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'You are not authenticated. Please log in.',
    //                 ], 401);
    //             }
    
    //             // Otherwise, redirect to login page (for non-API requests)
    //             return redirect()->guest(route('login'));
    //         }
    //     });
    // })
    // ->withMiddleware(function (Middleware $middleware) {
    //     $middleware->redirectGuestsTo('/login');
     
    //     // Using a closure...
    //     $middleware->redirectGuestsTo(function (Request $request) {
    //         return $request->expectsJson()
    //             ? response()->json(['message' => 'You are not authenticated. Please log in.'], 401)
    //             : redirect()->route('login');
    //         }
    //     );
    //     })
    // ->withMiddleware(function (Middleware $middleware) {
    //     $exceptions->render(function (OAuthServerException $e, Request $request) {
    //             // Custom response for OAuthServerException
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'The resource owner or authorization server denied the request.',
    //                 'error' => $e->getMessage(),
    //             ], $e->getStatusCode());
    //         });
    // })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
