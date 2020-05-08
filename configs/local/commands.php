<?php
declare(strict_types=1);

use App\Commands\ExampleCommand;
use App\Commands\HelloCommand;

return [

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Console Commands
    |--------------------------------------------------------------------------
    |
    | The console commands listed here will be automatically loaded in the
    | console. Feel free to add your own console commands to this array to
    | grant expanded functionality to your applications.
    |
    */
    HelloCommand::class,
    ExampleCommand::class,

];
