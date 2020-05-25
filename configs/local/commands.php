<?php
declare(strict_types=1);

use App\Commands\Demo\AddressesCommand;
use App\Commands\Demo\ExampleCommand;
use App\Commands\Demo\HelloCommand;

return [

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Console Commands
    |--------------------------------------------------------------------------
    */
    'hello' => HelloCommand::class,

    'example' => ExampleCommand::class,

    'addresses' => AddressesCommand::class,

];
