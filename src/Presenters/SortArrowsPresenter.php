<?php

namespace Witty\LaravelTableView\Presenters;

use Witty\LaravelTableView\LaravelTableView;

use Request;

class SortArrowsPresenter
{
	/**
     * Returns current uri with params for sorting by the specified property
     *
     * @param string $currentRouteName
     * @param string $currentSortFieldName
     * @param boolean $currentSortIsAscending
     * @param string $columnName
     * @return string
     */
	public static function anchorTagLink($currentRouteName, $currentSortFieldName, $currentSortIsAscending, $columnName)
	{
		$linkSortsAscending = $currentSortFieldName === $columnName ? ! $currentSortIsAscending : false;

		$routeParameters = [
			'sortedBy' => $columnName,
			'asc' 	   => $linkSortsAscending
		];

		$routeParameters = array_merge( 
			$routeParameters, 
			Request::except('page', 'sortedBy', 'asc') 
		);

		return route( $currentRouteName, $routeParameters );
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