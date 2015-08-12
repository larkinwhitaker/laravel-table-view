<?php

namespace Witty\LaravelTableView;

use Witty\LaravelTableView\Repositories\SearchRepository;
use Witty\LaravelTableView\Repositories\SortRepository;

use Witty\LaravelTableView\LaravelTableViewColumn;

use Witty\LaravelTableView\Presenters\LaravelTableViewPresenter;

use Request;
use Cookie;

class LaravelTableView 
{
	/**
     * @var \Illuminate\Database\Eloquent\Collection
     */
	private $dataCollection;

	/**
     * @var int
     */
	private $collectionSize;

	/**
     * @var array
     */
	private $columns;

	/**
     * @var string
     */
	private $headerControlView;

	/**
     * @var Witty\LaravelTableView\Repositories\SortRepository
     */
	private $sortRepo;

	/**
     * @var Witty\LaravelTableView\Repositories\SearchRepository
     */
	private $searchRepo;

	/**
	 * @var int
	 */
	private $perPage;

	/**
     * @var string
     */
	private $path;

	/**
     * @var string
     */
	private $tableName;

	/**
	 * @return void
	 */
	public function __construct()
	{
		// reference to current route
		$this->path = ltrim( Request::path(), '/');

		// sorting
		$this->sortRepo = new SortRepository;

		// pagination
		$this->perPage = $this->limitPerPage( $this->path );

		// search
		$this->searchRepo = new SearchRepository;
	}

	/**
     * Create a new table instance with Eloquent\Collection data and column mapping
     *
     * @param mixed $dataCollection - Illuminate\Database\Eloquent\Builder or (string) Eloquent Model Class Name
     * @param array $columns
     * @return Witty\LaravelTableView\LaravelTableView
     */
	public static function collection($dataCollection, $tableName = '')
	{
		$dataTable = new self;

		if ( is_string($dataCollection) )
		{
			$dataCollection = new $dataCollection();
		}

		$dataTable->tableName = $tableName ? $tableName 
			: class_basename( $dataCollection->getModel() );

		$dataTable->dataCollection = $dataCollection;
		$dataTable->columns = [];

		return $dataTable;
	}

	/**
     * Add additonal search fields
     *
     * @param array $searchFields
     * @return Witty\LaravelTableView\LaravelTableView
     */
	public function search($searchFields)
	{
		$this->searchRepo->field( $searchFields );

		return $this;
	}

	/**
     * Add a column to the table
     *
     * @param mixed $title
     * @param mixed $value
     * @return Witty\LaravelTableView\LaravelTableView
     */
	public function column($title, $value = null)
	{
		$newColumn = new LaravelTableViewColumn($title, $value);

		$this->columns[] = $newColumn;

		if ( $newColumn->isSearchable() )
		{
			$this->searchRepo->field( $newColumn->propertyName() );
		}
		if ( $newColumn->isDefaultSort() )
		{
			$this->sortRepo->setDefault($newColumn);
		}

		return $this;
	}

	/**
     * @return string
     */
	public function name()
	{
		return $this->tableName;
	}

	/**
     * Add a column to the table
     *
     * @param string $view
     * @param string $collectionAlias
     * @return Witty\LaravelTableView\LaravelTableView
     */
	public function headerControl($viewPath)
	{
		$this->headerControlView = $viewPath;

		return $this;
	}

	/**
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function data()
	{
		return $this->dataCollection;
	}

	/**
     * @return int
     */
	public function dataSize()
	{
		return $this->collectionSize;
	}

	/**
     * @return array
     */
	public function columns()
	{
		return $this->columns;
	}

	/**
     * @return array
     */
	public function headerView()
	{
		return $this->headerControlView;
	}

	/**
     * @return boolean
     */
	public function searchEnabled()
	{
		return $this->searchRepo->isEnabled();
	}

	/**
     * @return string
     */
	public function sortedBy()
	{
		return $this->sortRepo->sortedBy();
	}

	/**
     * @return boolean
     */
	public function sortAscending()
	{
		return $this->sortRepo->sortAscending();
	}

	/**
     * @return string
     */
	public function currentPath()
	{
		return $this->path;
	}

	/**
     * Paginate and build tableview for view
     *
     * @return Witty\LaravelTableView\LaravelTableView
     */
	public function build()
	{
		$this->dataCollection = $this->filteredAndSorted(
			$this->path,
			$this->dataCollection, 
			$this->searchRepo, 
			$this->sortRepo, 
			$this->columns 
		)->paginate( $this->perPage );

		$this->collectionSize = $this->dataCollection->total();

		return $this;
	}

	/**
     * Return helper class for subviews
     *
     * @return Witty\LaravelTableView\Presenters\LaravelTableViewPresenter
     */
	public function present()
	{
		return new LaravelTableViewPresenter($this);
	}

	/**
     * Filter collection by search query and order collection
     *
     * @param string $path
     * @param Illuminate\Database\Eloquent\Collection $dataCollection
     * @param Witty\LaravelTableView\Repositories\SearchRepository $searchRepo
     * @return Witty\LaravelTableView\Repositories\SortRepository $sortRepo
     * @return array $tableViewColumns
     * @return Illuminate\Database\Eloquent\Collection
     */
	private function filteredAndSorted( $path, $dataCollection, $searchRepo, $sortRepo, $tableViewColumns )
	{
		$dataCollection = $searchRepo->addSearch($dataCollection);

		if ( Cookie::has($path . '_sortedBy') )
		{
			$sortRepo->setDefaultFromCookie( 
				Cookie::get($path . '_sortedBy'),
				Cookie::get($path . '_sortAscending') 
			);
		}

		$dataCollection = $sortRepo->addOrder($dataCollection, $tableViewColumns);

		return $dataCollection;
	}

	/**
     * @param string $path
     * @return int
     */
	private function limitPerPage( $path )
	{
		$perPage = 10;

		if ( Request::has('limit') )
		{
			$perPage = Request::input('limit');
		}
		else if ( Cookie::has($path . '_perPage') )
		{
			$perPage = Cookie::get($path . '_perPage');
		}

		return $perPage;
	}

}


