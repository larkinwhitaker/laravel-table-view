<?php

namespace Witty\LaravelTableView\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;

class ExtendedLengthAwarePaginator extends LengthAwarePaginator 
{
	/**
	 * @return string
	 */
	public function updateLastPage( $currentPage, $lastPage )
	{
		$this->currentPage = $currentPage;
		$this->lastPage = $lastPage;
	}
}