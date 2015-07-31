
@if ( $tableView->searchEnabled() !== false )

	<form name="searchForm" class="pull-right" method="GET" action="{{ route($tableView->routeName(), Request::except('page', 'q')) }}">
		<input type="hidden" name="sortedBy" value="{{ $tableView->sortedBy() }}">
		<input type="hidden" name="asc" value="{{ $tableView->sortAscending() }}">
		<input type="hidden" name="limit" value="{{ Request::input('limit', 10) }}">
		<input type="hidden" name="page" value="0">

		<button type="submit" id="submit-search-btn" class="btn btn-success pull-right m-l-5">
			<i class="fa fa-search"></i>
		</button>

		<button type="button" id="cancel-search-btn" class="btn btn-xs btn-icon btn-circle btn-danger close">
			×
		</button>

		<div class="pull-right">
			<label>Search:
				<input id="search-form-input" class="search-form-input" type="search" name="q" value="{{ Request::get('q') }}">
			</label>
		</div>
	</form>

@endif