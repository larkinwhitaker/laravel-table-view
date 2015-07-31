<?php

namespace Witty\LaravelTableView\Presenters;

use Witty\LaravelTableView\LaravelTableView;

use Request;

class PerPageDropdownPresenter
{
	/**
     * Options for table view row count
     *
     * @var array
     */
	public static $pageLimitOptions = [10, 25, 50, 100];

	/**
     * Returns <option> tag with appropriate value and select attribute for the specified limit amount
     *
     * @param string $currentRouteName
     * @param int $optionTagLimit
     * @return string
     */
	public static function optionTag($currentRouteName, $optionTagLimit)
	{
		$routeParameters = Request::only('sortedBy', 'asc', 'q');
		$routeParameters['page'] = 1;
		$routeParameters['limit'] = $optionTagLimit;

		$tagValue = route( $currentRouteName, $routeParameters );

		$htmlTag = '<option value="' . $tagValue . '" ';

		$isSelected = ( $optionTagLimit === (int) Request::input('limit', 10) );

		if ( $isSelected ) $htmlTag .= 'selected ';

		return $htmlTag . '>' .  $optionTagLimit . '</option>';
	}
}