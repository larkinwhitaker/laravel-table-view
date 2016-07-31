<?php

namespace Witty\LaravelTableView\CookieStorage;

class UpdateStorage
{
	/**
     * @param \Illuminate\Http\Response $response
     * @param \Illuminate\Http\Request $request
     * @param string $currentPath
     * @return \Illuminate\Http\Response
     */
    public static function forResponse($response, $request, $currentPath)
    {
    	$tableViewSearchCookie = self::findValueOrForget($request, $currentPath . '.searchQuery', 'q');
		$response = $response->withCookie( $tableViewSearchCookie );

    	$tableViewPageCookie = self::findValueOrForget($request, $currentPath . '.currentPage', 'page');
		$response = $response->withCookie( $tableViewPageCookie );

		return $response;
    }

	/**
     * @param \Illuminate\Http\Response $response
     * @param \Illuminate\Http\Request $request
     * @param string $currentPath
     * @return \Illuminate\Http\Response
     */
    public static function forever($response, $request, $currentPath)
    {
		if ( $request->has('sortedBy') )
		{
			$response = $response
				->withCookie( cookie()->forever( $currentPath . '.sortedBy', $request->input('sortedBy') ) )
				->withCookie( cookie()->forever( $currentPath . '.sortAscending', $request->input('asc') ) );
		}

		if ( $request->has('limit') )
		{
			$response = $response
				->withCookie( cookie()->forever( $currentPath . '.perPage', $request->input('limit') ) );
		}

		return $response;
    }

	/**
     * @param \Illuminate\Http\Request $request
     * @param string $cookieName
     * @param string $requestKeyName
     * @return \Illuminate\Support\Facades\Cookie
     */
    private static function findValueOrForget($request, $cookieName, $requestKeyName)
    {
		if ( ! $request->has( $requestKeyName ) )
		{
			return cookie()->forget( $cookieName );
		}

		return cookie( $cookieName, $request->input( $requestKeyName ) );
    }
}
