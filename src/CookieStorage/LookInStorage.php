<?php

namespace Witty\LaravelTableView\CookieStorage;

class LookInStorage
{
	/**
     * @param array $input
     * @param mixed $storedSearchQuery
     * @param mixed $storedPageNumber
     * @return array
     */
    public static function forRedirectParameters($input, $storedSearchQuery, $storedPageNumber, $storedPerPage)
    {
		if ( $storedSearchQuery ) 
		{
			$input['q'] = $storedSearchQuery;
		}

		if ( $storedPageNumber ) 
		{
			$input['page'] = $storedPageNumber;
		}

		if ( $storedPerPage ) 
		{
			$input['limit'] = $storedPerPage;
		}

		return $input;
    }

	/**
     * @param \Illuminate\Http\Request $request
     * @param string $currentPath
     * @return mixed
     */
    public static function forSearch($request, $currentPath)
    {
    	if ( ! $request->has('q') 
    		&& $request->input('q') !== '' 
    		&& $request->cookie($currentPath . '_searchQuery') )
		{
			return $request->cookie($currentPath . '_searchQuery');
		}

		return false;
    }

	/**
     * @param \Illuminate\Http\Request $request
     * @param string $currentPath
     * @return mixed
     */
    public static function forPage($request, $currentPath)
    {
		if ( ! $request->has('page') 
			&& $request->input('page') !== '0' 
			&& $request->cookie($currentPath . '_currentPage') )
		{
			return $request->cookie($currentPath . '_currentPage');
		}

		return false;
    }

	/**
     * @param \Illuminate\Http\Request $request
     * @param string $currentPath
     * @return mixed
     */
    public static function forLimit($request, $currentPath)
    {
		if ( ! $request->has('limit')
			&& $request->cookie($currentPath . '_perPage') )
		{
			return $request->cookie($currentPath . '_perPage');
		}

		return false;
    }
}
