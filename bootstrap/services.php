<?php
declare(strict_types=1);

use App\Kernel\Services\Logger\LoggerServiceProvider;
use App\Kernel\Services\Configs\ConfigsServiceProvider;

return [

    ConfigsServiceProvider::class,
    LoggerServiceProvider::class,

];
