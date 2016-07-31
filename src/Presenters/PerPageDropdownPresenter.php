<?php

namespace Witty\LaravelTableView\Presenters;

use Witty\LaravelTableView\LaravelTableView;
use Witty\LaravelTableView\Presenters\RoutePresenter;

use Request;

class PerPageDropdownPresenter
{
	/**
     * Options for table view row count
     *
     * @var array
     */
	private static $pageLimitOptions = [10, 25, 50, 100];

	/**
     * Returns <option> tag with appropriate value and select attribute for the specified limit amount
     *
     * @param int $optionTagLimit
     * @return string
     */
	public static function pageLimitOptions($dataCollectionSize)
	{
		$currentLimit = (int) Request::input('limit', 10);
		$totalOptions = count( self::$pageLimitOptions );

		$htmlSelectOptions = [];

		for ( $i=0; $i<$totalOptions; $i++ )
		{
			$pageLimit = self::$pageLimitOptions[$i];

			if ( 
				$pageLimit <= $dataCollectionSize 
				|| $pageLimit <= $currentLimit
				|| ( $i >= 1 && self::$pageLimitOptions[$i-1] < $dataCollectionSize ) 
			) 
			{
				$htmlSelectOptions[] = $pageLimit;
			}
		}

		return $htmlSelectOptions;
	}

	/**
     * Returns <option> tag with appropriate value and select attribute for the specified limit amount
     *
     * @param string $currentPath
     * @param int $optionTagLimit
     * @return string
     */
	public static function optionTag($currentPath, $optionTagLimit)
	{
		$routeParameters = array_merge([
				'page'  => 1,
				'limit' => $optionTagLimit
			], Request::except('page', 'limit') 
		);

		$htmlTag = '<option value="' . RoutePresenter::withParam($currentPath, $routeParameters) . '" ';

		$currentLimit = (int) Request::input('limit', 10);

		if ( $optionTagLimit === $currentLimit ) 
		{
			$htmlTag .= 'selected ';
		}

		return $htmlTag . '>' .  $optionTagLimit . '</option>';
	}
}