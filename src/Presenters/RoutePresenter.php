<?php

namespace Witty\LaravelTableView\Presenters;

class RoutePresenter
{
	/**
     * Returns current uri with params
     *
     * @param string $currentPath
     * @param array $routeParameters
     * @return string
     */
	public static function withParam($currentPath, $routeParameters)
	{
		return url("/"
			. $currentPath .'?'
            . http_build_query($routeParameters, null, '&'));
	}
}
