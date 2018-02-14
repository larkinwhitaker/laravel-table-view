<?php

namespace Witty\LaravelTableView\Facades;

use Illuminate\Support\Facades\Facade;

class TableView extends Facade 
{
    /**
    * Get the registered name of the component.
    *
    * @return string
    */
    protected static function getFacadeAccessor() 
    {
        return 'Witty\LaravelTableView\laravelTableView';
    }

}