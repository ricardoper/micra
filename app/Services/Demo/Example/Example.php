<?php
declare(strict_types=1);

namespace App\Services\Demo\Example;

class Example
{

    /**
     * Capitalize Name
     *
     * @param string $name
     * @return string
     */
    public function capitalize(string $name): string
    {
        return ucwords($name);
    }
}
