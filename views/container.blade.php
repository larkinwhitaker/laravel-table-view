<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse m-b-0">

            <div class="panel-heading">
                <h4 class="panel-title">
                	{{ $tableView->present()->title() }}
                </h4>
            </div>
            
            <div class="panel-body">

            	<div class="m-b-10 clearfix">
            		
                	@include('table-view::elements._per_page_dropdown')
			    
				    @include('table-view::elements._search_form')

				    @if ( $tableView->hasHeaderView() )
				    	<div class="pull-right">
				    		{{ $tableView->headerView() }}
				    	</div>
				    @endif
            	</div>

                <div class="table-responsive">

                	@include( $tableView->present()->table() )

                </div>
            </div>
        </div>

		<div class="pull-right">
			<?php echo $tableView->data()->appends( Request::except('page') )->render(); ?>
		</div>

    </div>
</div>