<script>

	var searchQuery = "<?php echo addslashes( Request::get('q') ); ?>";

	function toggleSearchButtons( showButton )
	{
		if ( showButton ) $('#cancel-search-btn').show();
		else $('#cancel-search-btn').hide();

		if ( searchQuery && ! showButton ) showButton = true;
		
		$('#submit-search-btn').prop('disabled', ! showButton);
	}

	$(document).ready(function() 
	{
		toggleSearchButtons( searchQuery );
		
		$('#per-page-dropdown').on('change', function() 
		{
			window.location.href = this.value;
		});

		$('#search-form-input').on('input', function() 
		{
			toggleSearchButtons( $(this).val() );
		});
		$('#cancel-search-btn').on('click', function() 
		{
			$('#search-form-input').val('');
			toggleSearchButtons( false );
			if ( searchQuery != '' ) searchForm.submit();
		});

	});

</script>