<?php

namespace Witty\LaravelTableView\Repositories;

use Request;

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
		$this->defaultSort = [
			'property' => $column->propertyName(),
			'isAscending' => $column->defaultSortingDirectionIsAscending()
		];
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

		return $dataCollection->orderBy( $this->sortedBy, $this->sortAscending ? 'ASC' : 'DESC');
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