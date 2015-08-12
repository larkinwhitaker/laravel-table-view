# laravel-table-view

Laravel 5 Package for easily displaying table views for Eloquent Collections with search and sort functionality built in.

Installation
----

Update your `composer.json` file to include this package as a dependency
```json
"witty/laravel-table-view": "dev-master"
```


Register the TableView service provider by adding it to the providers array in the `config/app.php` file.
```php
'providers' => array(
    Witty\LaravelTableView\LaravelTableViewServiceProvider::class
)
```

If you want you can alias the TableView facade by adding it to the aliases array in the `config/app.php` file.
```php
'aliases' => array(
        'TableView' => Witty\LaravelTableView\Facades\TableView::class,
)
```

# Configuration

Copy the vendor file views and assets into your project by running
```
php artisan vendor:publish
```

This will add multiple styles and one script to public/vendor/table-view
The plugin depends on jQuery and v1.9.1 will be included under public/vendor/table-view
	- Bootstrap CSS v3.3.2
	- Font Awesome v4.3.0
	- jQuery v1.9.1



# Usage

Initialize the table view by passing in an instance of \Illuminate\Eloquent\Builder or simply the class name of the model for the tableview
```php

	$users = User::select('id', 'name', 'email', 'created_at');

	$usersTableView = TableView::collection( $users )
	// or $usersTableView = TableView::collection( \App\User::class )

```

Adding Columns to the tableview
```php

	$usersTableView = $usersTableView
		// you can pass in the title for the column, and the Eloquent\Model property name
		->column('Email', 'email')

		// Add a colon after the Eloquent\Model property name along with sort and/or search to enable these options
		->column('Name', 'name:sort,search')

		// Set the default sorting property with 
		->column('Name', 'name:sort*,search')	// Sorted Ascending by default or specify
		->column('Name', 'name:sort*:asc')
		->column('Name', 'name:sort*:desc')

		// Custom column values are created by passing an array with the Eloquent\Model property name as the key
		//  and a closure function
		->column('Joined At', ['created_at:sort*' => function ($user) 
		{
			return $user->created_at->diffForHumans();
		}])



		// OR
		->column(function ($user) 
		{
			return '<img src="' . $user->image_path . '" height="60" width="60">';
		})
		->column('Email', 'email:sort,search')
		->column(function ($user) 
		{
			return '<a class="btn btn-success" href="/users/' . $user->id . '">View</a>';
		});

```

Custom column values
```php

	$usersTableView = $usersTableView
		// You can pass in an array for the column's row value with the Eloquent\Model property name as the key
		//  and a closure function
		->column('Joined At', ['created_at:sort*' => function ($user) 
		{
			return $user->created_at->diffForHumans();
		}])

		// OR if sorting and searching is unnecessary, simply pass in the Closure instead of the array
		->column('Image', function ($user) 
		{
			return '<img src="' . $user->image_path . '" height="60" width="60">';
		});
}]);

```

Columns without titles
```php

	$usersTableView = $usersTableView
		// Just leave the column title out if you don't want to use it
		->column(function ($user) 
		{
			return '<img src="' . $user->image_path . '" height="60" width="60">';
		});

```

Additional Controls - you can add partial views containing custom controls like a filter button to add additional functionality to your table
```php
	$usersTableView = $usersTableView
		// Just pass in the partial view file path of the custom control
		->headerControl('_types_filter_button');

		// access the TableView data collection with $usersTableView->data()

```

Finally, build the TableView and pass it to the view
```php

	$usersTableView = $usersTableView->build();

	return view('test', [
		'usersTableView' => $usersTableView
	]);

```

All together with chaining
```php

Route::get('/', function(\Illuminate\Http\Request $request) 
{
	$users = User::select('id', 'name', 'email', 'created_at');

	$usersTableView = TableView::collection( $users, 'Administrator' )
		->column(function ($user) 
		{
			return '<img src="' . $user->image_path . '" height="60" width="60">';
		})
		->column('Name', 'name:sort,search')
		->column('Email', 'email:sort,search')
		->column('Joined At', ['created_at:sort*' => function ($user) 
		{
			return $user->created_at->diffForHumans();
		}])
		->column(function ($user) 
		{
			return '<a class="btn btn-success" href="/users/' . $user->id . '">View</a>';
		})
		->headerControl('_types_filter_button')
		->build();

	return view('test', [
		'usersTableView' => $usersTableView
	]);
});

```
# Front End
Include stylesheets for Bootstrap and Font Awesome
	- Bootstrap CSS v3.3.2 and Font Awesome v4.3.0 are included in the vendor
```html

<link href="{{ asset('vendor/table-view/bootstrap.min.css') }}" rel="stylesheet" />
<link href="{{ asset('vendor/table-view/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
<link href="{{ asset('vendor/table-view/css/themes/tableview-a.css') }}" rel="stylesheet" />

```

Include the tablview in your view, referencing the variable name given to it
```html

@include('table-view::container', ['tableView' => $usersTableView])

```

Also include the tablview scripts
	** Requires jQuery and v1.9.1 will be included under public/vendor/table-view
```html

<script src="{{ asset('vendor/table-view/js/jquery-1.9.1.min.js') }}"></script>

@include('table-view::scripts')

```

# Middleware Cookie Storage
Selected options for the tableview are easily added to cookie storage with built-in Middleware.  

Sort options and limits per page are each added to permanent storage.  At any point, a user returning to the tableview will see these options filled with the same values that he/she selected in his/her most recent session.  

The search query and page number are temporarily stored during the user's current session.  With this, a user could visit something http://tableview.com/blog-articles with the tableview listing articles.  When a user views a specific article like http://tableview.com/blog-articles/laravel-blog/article, any link back to http://tableview.com/blog-articles will show the tableview with its most recent page number and search query.

All you have to do:

Edit app/Http/Kernel.php, adding a reference to the Middleware
```php

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,

        // Laravel TableView Middleware
        'table-view.storage' => \Witty\LaravelTableView\Middleware\TableViewCookieStorage::class,
    ];
```

Then add it to the route containing the tableview
```php

    Route::get('/', ['middleware' => 'table-view.storage', function () {

```

# That's it!
It's particular but in just a few lines you have a dynamic table view with powerful functionality.  Feel free to customize the tableview and element partial views.  Additional themes and styles coming soon.
