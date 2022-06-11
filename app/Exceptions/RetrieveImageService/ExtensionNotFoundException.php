<?php

namespace App\Exceptions\RetrieveImageService;

use Exception;

class ExtensionNotFoundException extends Exception
{
    public function __construct(string $filename)
    {
        parent::__construct('Cannot guess extension on retrieved file ' . $filename, 1001, null);
    }
}
