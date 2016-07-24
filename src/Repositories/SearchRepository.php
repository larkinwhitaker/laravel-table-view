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
			$this->searchFields = array_merge($this->searchFields, $searchFields);
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

	private static function groupFields($fields) {
            $result = [];

            foreach($fields as $field) {
                $parent = '_self';
                $name = '';
                $chunks = explode('.', $field);

                $c = count($chunks);
                if ($c > 1) {
                    $name = $chunks[$c - 1];
                    $parent = implode('.', array_splice($chunks, 0, $c - 1));
                }else {
                    $name = $chunks[0];
                }

                if (!isset($result[$parent])) {
                    $result[$parent] = [];
                }
                $result[$parent][] = $name;
            }

            return $result;

        }

        static function applyFilter($query, $fields, $search, $useOr = true) {
            foreach ($fields as $index => $field) {
                $fn = $index > 0 || $useOr  ? 'orWhere' : 'where';
                $query = $query->{$fn}($field, 'LIKE', "%$search%");
            }
            return $query;
        }

        static function searchAssociation($query, $path, $fields, $search, $useOr = true) {
            $fn = $useOr ? 'orWhereHas' : 'whereHas';
            $idx = strpos($path, '.');
            if ($idx === false) {
                return $query->{$fn}($path, function ($q) use ($fields, $search) {
                    static::applyFilter($q, $fields, $search);
                });
            }

            $name = substr($path, 0, $idx);
            $children = substr($path, $idx + 1);
            return $query->{$fn}($name, function ($q) use ($children, $fields, $search) {
                static::searchAssociation($q, $children, $fields, $search, true);
            });
        }

        /**
         * Filter data collection for search query if there is one
         *
         * @param \Illuminate\Database\Eloquent\Collection $dataCollection
         * @param array
         * @return \Illuminate\Database\Eloquent\Collection
         */
        public function addSearch($dataCollection) {
          if ( ! $this->searchQuery ) {
              return $dataCollection;
          }
          $searchableFields = $this->searchFields;


          return $dataCollection->where(function ($query) use ($searchableFields) {
              $i = 0;
              $groupedFields = static::groupFields($searchableFields);

              foreach ($groupedFields as $parent => $fields) {
                  if('_self' == $parent) {
                      $query = static::applyFilter($query, $fields, $this->searchQuery, $i > 0);
                  }else {
                      $query = static::searchAssociation($query, $parent, $fields, $this->searchQuery, $i > 0);
                  }
                  $i++;
              }
          });
      }
}
