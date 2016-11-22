<?php

namespace Witty\LaravelTableView\Repositories;

use Request;
// use Illuminate\Cookie\Cookie;

class SortRepository 
{
	/**
	 * @var array
	 */
	private $defaultSort;

	/**
     * @var string
     */
	private $sortedBy;

	/**
     * @var boolean
     */
	private $sortAscending;

	/**
     * @param \Witty\LaravelTableView\LaravelTableViewColumn $column
     * @return void
     */
	public function setDefault($column)
	{
		$this->newDefaults(
			$column->propertyName(), 
			$column->defaultSortingDirectionIsAscending()
		);
	}

	/**
     * @param string $propertyName
     * @param boolean $isAscending
     * @return void
     */
	public function setDefaultFromCookie($propertyName, $isAscending)
	{
		$this->newDefaults($propertyName, $isAscending);
	}

	/**
     * @return string
     */
	public function sortedBy()
	{
		return $this->sortedBy;
	}

	/**
     * @return string
     */
	public function sortAscending()
	{
		return $this->sortAscending;
	}

	/**
     * @param \Illuminate\Database\Eloquent\Collection $dataCollection
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function addOrder($dataCollection, $columns)
	{
		if ( ! isset($this->defaultSort['property']) )
		{
			$this->defaultSort = $this->findADefault($columns);
		}

		$this->sortedBy = Request::input('sortedBy', $this->defaultSort['property']);
		$this->sortAscending = Request::input('asc', $this->defaultSort['isAscending']);

		if ( ! $this->sortedBy) {
			return $dataCollection;
		}

		$sortField = $this->sortedBy;
		if (strpos($sortField, '{') !== false) {
			$sortField = str_replace('{', '', $sortField);
			$sortField = str_replace('}', '', $sortField);

			$sortField = \DB::raw($sortField);
		}

		return $dataCollection->orderBy( $sortField, $this->sortAscending ? 'ASC' : 'DESC');
	}

	/**
     * @param string $propertyName
     * @param boolean $isAscending
     * @return void
     */
	private function newDefaults($propertyName, $isAscending)
	{
		$this->defaultSort = [
			'property' 	  => $propertyName,
			'isAscending' => $isAscending
		];
	}

	/**
     * @param array $columns
     * @return array
     */
	private function findADefault($columns)
	{
		foreach($columns as $column)
		{
			if ( $column->isSortable() )
			{
				return [
					'property' 	  => $column->propertyName(),
					'isAscending' => true
				];
			}
		}
	}
}