<?php

namespace Witty\LaravelTableView\Middleware;

use Closure;
use Witty\LaravelTableView\Middleware\LookInStorage;
use Witty\LaravelTableView\Middleware\UpdateStorage;


class TableViewCookieStorage
{
	/**
     * @var string 
     */
	private $redirectRoute;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	$currentRouteName = $request->route()->getName();

    	$shouldRedirectWithViewParamsFromCookieStorage = $this->beforeMiddleware($request, $currentRouteName);

		if ( $shouldRedirectWithViewParamsFromCookieStorage )
		{
			return redirect( $this->redirectRoute );
		}

		$response = $next($request);

        return $this->afterMiddleware($request, $response, $currentRouteName);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $currentRouteName
     * @return boolean
     */
    private function beforeMiddleware($request, $currentRouteName)
    {
		$storedSearchQuery = LookInStorage::forSearch($request, $currentRouteName);
    	$storedPageNumber = LookInStorage::forPage($request, $currentRouteName);

		$shouldRedirect = ( (bool) $storedSearchQuery || (bool) $storedPageNumber );

		if ( $shouldRedirect )
		{
			$redirectParameters = LookInStorage::forRedirectParameters($request, $storedSearchQuery, $storedPageNumber);
			$this->redirectRoute = route( $currentRouteName, $redirectParameters );
		}

		return $shouldRedirect;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @param string $currentRouteName
     * @return \Illuminate\Http\Response
     */
    private function afterMiddleware($request, $response, $currentRouteName)
    {
    	$response = UpdateStorage::forResponse($response, $request, $currentRouteName);

    	$response = UpdateStorage::forever($response, $request, $currentRouteName);

		return $response;
    }
}
