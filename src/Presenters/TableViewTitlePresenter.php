<?php

namespace Witty\LaravelTableView\Presenters;

use Request;

class TableViewTitlePresenter
{
	/**
     * @param Witty\LaravelTableView\LaravelTableView $laravelTableView
     * @param int $dataCollectionSize
     * @return string
     */
	public static function formattedTitle( $laravelTableView, $dataCollectionSize )
	{
		$modelName = $laravelTableView->name();

		if ( $dataCollectionSize !== 1 )
		{
			$modelName = str_plural( $modelName );
		}

		return self::titleWithTableFilters( $modelName, $dataCollectionSize );
	}

	/**
     * @param string $modelName
     * @param int $dataCollectionSize
     * @return string
     */
	private static function titleWithTableFilters( $modelName, $dataCollectionSize )
	{
		$title = $dataCollectionSize > 0 ? number_format($dataCollectionSize) : 'No';

		if ( ! Request::has('q') )
		{
			return $title . ' Total ' . $modelName;
		}

		return $title . ' ' . $modelName . ' found by searching ' . Request::get('q');
	}
}