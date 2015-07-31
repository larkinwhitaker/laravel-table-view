<?php

namespace Witty\LaravelTableView\Pagination;

use Request;
use Witty\LaravelTableView\Pagination\ExtendedLengthAwarePaginator;

class LaravelTableViewPaginator
{
	/**
	 * @var int
	 */
	private $pageNumber;

	/**
	 * @var int
	 */
	private $perPage;

	/**
	 * @return void
	 */
	public function __construct()
	{
		$this->pageNumber = Request::input('page', 1);
		$this->perPage = Request::input('limit', 10);
	}

	/**
	 * @return string
	 */
	public function paginateCollection( $dataCollection, $collectionSize, $routeName )
	{
		$dataCollection = $dataCollection->skip( ($this->pageNumber - 1) * $this->perPage )
			->take( $this->perPage )->get();

		$paginator = new ExtendedLengthAwarePaginator(
			$dataCollection,
			$collectionSize / $this->perPage,
			$this->perPage
		);

		$paginator->setPath( route( $routeName ) );

		$paginator->updateLastPage(
			$this->pageNumber,
			ceil( $collectionSize / $this->perPage )
		);

		return $paginator;
	}
}