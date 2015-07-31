<?php

namespace Witty\LaravelTableView\Presenters;

use Witty\LaravelTableView\LaravelTableView;

use Witty\LaravelTableView\Presenters\PerPageDropdownPresenter;
use Witty\LaravelTableView\Presenters\SortArrowsPresenter;

use Request;

class LaravelTableViewPresenter
{
	/**
     * @var Witty\LaravelTableView\LaravelTableView
     */
	private $laravelTableView;

	/**
     * @param Witty\LaravelTableView\LaravelTableView $laravelTableView
     * @return void
     */
	public function __construct(LaravelTableView $laravelTableView)
	{
		$this->laravelTableView = $laravelTableView;
	}

	/**
     * Returns title for tableview panel header
     *
     * @return string
     */
	public function title()
	{
		$dataCollectionSize = $this->laravelTableView->dataSize();

		$modelName = $this->modelNameForTitle( $this->laravelTableView, $dataCollectionSize );

		return $this->titleWithTableFilters( $modelName, $dataCollectionSize );
	}

	/**
     * Returns view file name for given dataset: empty or filled
     *
     * @return string
     */
	public function table()
	{
		$tableViewFileName =  $this->laravelTableView->dataSize() ? '_filled' : '_empty';

		return 'table-view::partials.' . $tableViewFileName;
	}

	/**
     * Per Page Dropdown 
     * Returns Options for table view row count
     *
     * @return array
     */
	public function perPageOptions()
	{
		return PerPageDropdownPresenter::$pageLimitOptions;
	}

	/**
     * Per Page Dropdown 
     * Returns <option> tag with appropriate value and select attribute for the specified limit amount
     *
     * @param int $optionTagLimit
     * @return string
     */
	public function perPageOptionTagFor( $optionTagLimit )
	{
		return PerPageDropdownPresenter::optionTag(
			$this->laravelTableView->routeName(), 
			$optionTagLimit
		);
	}

	/**
     * Sort Arrows Button
     * Returns current uri with params for sorting by the specified property
     *
     * @param string $columnName
     * @return string
     */
	public function sortArrowAnchorTagLinkForColumnWithName($columnName)
	{
		return SortArrowsPresenter::anchorTagLink(
			$this->laravelTableView->routeName(), 
			$this->laravelTableView->sortedBy(), 
			$this->laravelTableView->sortAscending(), 
			$columnName
		);
	}

	/**
     * Sort Arrows Button
     * Returns font awesome icon class name : fa-sort,fa-sort-asc,fa-sort-desc
     *
     * @param string $columnName
     * @return string
     */
	public function sortArrowIconClassForColumnWithName($columnName)
	{
		return SortArrowsPresenter::iconClassName(
			$this->laravelTableView->sortedBy(), 
			$this->laravelTableView->sortAscending(), 
			$columnName
		);
	}

	/**
     * @param Witty\LaravelTableView\LaravelTableView $laravelTableView
     * @param int $dataCollectionSize
     * @return string
     */
	private function modelNameForTitle( $laravelTableView, $dataCollectionSize )
	{
		$modelName = $laravelTableView->name();

		if ( $dataCollectionSize !== 1 )
		{
			$modelName = str_plural( $modelName );
		}

		return $modelName;
	}

	/**
     * @param string $modelName
     * @param int $dataCollectionSize
     * @return string
     */
	private function titleWithTableFilters( $modelName, $dataCollectionSize )
	{
		$title = $dataCollectionSize > 0 ? $dataCollectionSize : 'No';

		if ( ! Request::has('q') )
		{
			return $title . ' Total ' . $modelName;
		}

		return $title . ' ' . $modelName . ' found by searching ' . Request::get('q');
	}
}