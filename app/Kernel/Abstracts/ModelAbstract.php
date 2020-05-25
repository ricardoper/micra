<?php
declare(strict_types=1);

namespace App\Kernel\Abstracts;

abstract class ModelAbstract extends CommandAbstract
{

    /**
     * Get Database Service
     *
     * @return mixed
     */
    protected function getDb()
    {
        return $this->container['db'] ?? null;
    }
}
