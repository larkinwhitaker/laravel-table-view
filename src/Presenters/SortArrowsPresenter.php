<?php

namespace Witty\LaravelTableView\Presenters;

use Witty\LaravelTableView\LaravelTableView;
use Witty\LaravelTableView\Presenters\RoutePresenter;

use Request;

class SortArrowsPresenter
{
	/**
     * Returns current uri with params for sorting by the specified property
     *
     * @param string $currentPath
     * @param string $currentSortFieldName
     * @param boolean $currentSortIsAscending
     * @param string $columnName
     * @return string
     */
	public static function anchorTagLink($currentPath, $currentSortFieldName, $currentSortIsAscending, $columnName)
	{
		$linkSortsAscending = $currentSortFieldName === $columnName ? ! $currentSortIsAscending : false;

		$routeParameters = array_merge([
				'sortedBy' => $columnName,
				'asc' 	   => $linkSortsAscending
			], Request::except('sortedBy', 'asc') 
		);

		return RoutePresenter::withParam($currentPath, $routeParameters);
	}

	/**
     * Returns font awesome icon class name : fa-sort,fa-sort-asc,fa-sort-desc
     *
     * @param string $currentSortFieldName
     * @param boolean $currentSortIsAscending
     * @param string $columnName
     * @return string
     */
	public static function iconClassName($currentSortFieldName, $currentSortIsAscending, $columnName)
	{
		$className = "fa fa-sort";

		if ( $currentSortFieldName === $columnName )
		{
			$directionName = $currentSortIsAscending ? 'asc' : 'desc';
			$className .= "-" . $directionName;
		}

		return $className;
	}
}