<?php

namespace Witty\LaravelTableView\Repositories;

use Request;

class SearchRepository 
{
	/**
	 * @var string
	 */
	private $searchQuery;

	/**
	 * @var array
	 */
	private $searchFields = [];

	/**
	 * @return void
	 */
	public function __construct()
	{
		$this->searchQuery = Request::input('q', '');
	}

	/**
     * @param mixed $searchField
     * @return void
     */
	public function field($searchField)
	{
		if ( is_string($searchField) )
		{
			$this->searchFields[] = $searchField;
		}
		else if ( is_array($searchField) )
		{
			$this->searchFields = array_merge($this->searchFields, $searchField);
		}
	}

	/**
     * @return boolean
     */
	public function isEnabled()
	{
		return (bool) count($this->searchFields);
	}

	/**
     * @return string
     */
	public function queryString()
	{
		return $this->searchQuery;
	}

	/**
     * Filter data collection for search query if there is one
     *
     * @param \Illuminate\Database\Eloquent\Collection $dataCollection
     * @param array
     * @return \Illuminate\Database\Eloquent\Collection
     */
	public function addSearch($dataCollection)
	{
		if ( ! $this->searchQuery ) return $dataCollection;

		$i = 0;
		foreach ($this->searchFields as $searchableProperty)
		{
			$whereClause = ($i === 0) ? 'where' : 'orWhere';

			$dataCollection = $dataCollection->{$whereClause}($searchableProperty, 'LIKE', "%{$this->searchQuery}%");
			$i++;
		}

		return $dataCollection;
	}
}