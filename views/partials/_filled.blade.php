

<table class="table table-striped table-bordered m-b-0">
    <thead>
        <tr>

        	@foreach($tableView->columns() as $column)
	            <th>
	            	{{ $column->title() }}

	            	@if ( $column->isSortable() )

	            		@include('table-view::elements._sort_arrows', ['columnName' => $column->propertyName()])

	            	@endif
	            </th>
            @endforeach
            
        </tr>
    </thead>
    <tbody>

       @foreach($tableView->data() as $dataModel)
           <tr>

           		@foreach($tableView->columns() as $column)
	               	<td>
	               		<?php echo $column->rowValue($dataModel); ?>
	               	</td>
   				@endforeach
   				
           	</tr>
   		@endforeach

    </tbody>
</table>