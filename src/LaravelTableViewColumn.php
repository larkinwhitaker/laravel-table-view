<?php

namespace Witty\LaravelTableView;

class LaravelTableViewColumn
{
	/**
     * @var string
     */
	private $title;

	/**
     * @var string
     */
	private $propertyName;

	/**
     * @var Closure
     */
	private $customValue;

	/**
     * @var boolean
     */
	private $sortable;

	/**
     * @var boolean
     */
	private $sortDefault;

	/**
     * @var boolean
     */
	private $searchable;

	/**
     * Build the column
     *
     * @param mixed $title
     * @param mixed $value
     * @return void
     */
	public function __construct($title, $value)
	{
		$this->sortable = false;
		$this->sortDefault = false;
		$this->searchable = false;

		if ( is_null($value) )
		{
			$value = $title;
			$title = '';
		}

		$this->title = ( $title === false ) ? '' : $title;

		$this->debunkInputValue($value);
	}

	/**
     * Get the title for the column header
     *
     * @return string
     */
	public function title()
	{
		return $this->title;
	}

	/**
     * Get the property name for the corresponding column and data model->property
     *
     * @return string
     */
	public function propertyName()
	{
		return $this->propertyName;
	}

	/**
     * Get the value for a specific row in the table
     *
     * @param \Illuminate\Database\Eloquent\Model
     * @return string
     */
	public function rowValue($model)
	{
		if ( ! isset($this->customValue) )
		{
			return $model->{$this->propertyName};
		}
		else
		{
			$closure = $this->customValue;
			return $closure($model);
		}
	}

	/**
     * Get whether or not the column is sortable by its property
     *
     * @return boolean
     */
	public function isSortable()
	{
		return $this->sortable;
	}

	/**
     * Get whether or not the column contains the default property for sorting
     *
     * @return mixed
     */
	public function isDefaultSort()
	{
		return $this->sortDefault;
	}

	/**
     * Get whether or not the column containing the default property for sorting is ascending
     *
     * @return mixed
     */
	public function defaultSortingDirectionIsAscending()
	{
		return $this->defaultSortIsAscending;
	}

	/**
     * Get whether or not the column is searchable by its property
     *
     * @return boolean
     */
	public function isSearchable()
	{
		return $this->searchable;
	}

	/**
     * Get various column attributes from the input value
     *
     * @param mixed $value
     * @return void
     */
	private function debunkInputValue($value)
	{
		if ( is_string($value) )
		{
			$this->parseValueStringForOptions($value);
		}
		else if ( is_array($value) )
		{
			foreach ($value as $propertyWithOptions => $columnValue)
			{
				$this->parseValueStringForOptions($propertyWithOptions);
				$this->customValue = $columnValue;
			}
		}
		else
		{
			$this->customValue = $value;
		}
	}

	/**
     * Get the property name and options from the input value
     *
     * @param string $value
     * @return void
     */
	private function parseValueStringForOptions($value)
	{
		$optionsStart = strpos($value, ':');
		$this->propertyName = substr($value, 0, $optionsStart);

		$options = explode(",", substr($value, $optionsStart+1));

		foreach ($options as $option)
		{
			switch ($option)
			{
				case 'sort': 
					$this->sortable = true; break;
				case 'sort*':
				case 'sort*:asc': 
					$this->sortable = true; $this->sortDefault = true; $this->defaultSortIsAscending = true; break;
				case 'sort*:desc': 
					$this->sortable = true; $this->sortDefault = true; $this->defaultSortIsAscending = false; break;
				case 'search': 
					$this->searchable = true; break;
				default: 
					throw new \Exception('Invalid argument for column value.  Allowed values are sort and search.'); break;
			}
		}
	}
}