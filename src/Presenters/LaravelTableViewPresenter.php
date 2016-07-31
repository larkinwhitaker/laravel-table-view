<?php

namespace Witty\LaravelTableView\Presenters;

use Witty\LaravelTableView\LaravelTableView;

use Witty\LaravelTableView\Presenters\TableViewTitlePresenter;
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

		return TableViewTitlePresenter::formattedTitle( $this->laravelTableView, $dataCollectionSize );
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
		return PerPageDropdownPresenter::pageLimitOptions( 
			$this->laravelTableView->dataSize() 
		);
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
			$this->laravelTableView->currentPath(),
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
			$this->laravelTableView->currentPath(),
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
}