<?php

namespace Witty\LaravelTableView\Middleware;

use Closure;
use Witty\LaravelTableView\CookieStorage\LookInStorage;
use Witty\LaravelTableView\CookieStorage\UpdateStorage;
use Witty\LaravelTableView\Presenters\RoutePresenter;


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
    	$currentPath = $request->path();

    	$shouldRedirectWithViewParamsFromCookieStorage = $this->beforeMiddleware($request, $currentPath);

		if ( $shouldRedirectWithViewParamsFromCookieStorage )
		{
			return redirect( $this->redirectRoute );
		}

		$response = $next($request);

        return $this->afterMiddleware($request, $response, $currentPath);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $currentPath
     * @return boolean
     */
    private function beforeMiddleware($request, $currentPath)
    {
		$storedSearchQuery = LookInStorage::forSearch($request, $currentPath);
    	$storedPageNumber = LookInStorage::forPage($request, $currentPath);
    	$storedPerPage = LookInStorage::forLimit($request, $currentPath);

    	
    	\Log::info([
    		'storage' => [
	    		'search' => $storedSearchQuery,
	    		'page' => $storedPageNumber,
	    		'per page' => $storedPerPage
			],
				'request' => [
	    		'search' => $request->has('q'),
	    		'page' => $request->has('page'),
	    		'per page' => $request->has('limit')
    		]
		]);
		$shouldRedirect = ( (bool) $storedSearchQuery || (bool) $storedPageNumber || (bool) $storedPerPage );

		if ( $shouldRedirect )
		{
			$redirectParameters = LookInStorage::forRedirectParameters($request->all(), $storedSearchQuery, $storedPageNumber,  $storedPerPage);
			$this->redirectRoute = RoutePresenter::withParam( $currentPath, $redirectParameters );
		}

		return $shouldRedirect;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @param string $currentPath
     * @return \Illuminate\Http\Response
     */
    private function afterMiddleware($request, $response, $currentPath)
    {
    	$response = UpdateStorage::forResponse($response, $request, $currentPath);

    	$response = UpdateStorage::forever($response, $request, $currentPath);

		return $response;
    }
}
