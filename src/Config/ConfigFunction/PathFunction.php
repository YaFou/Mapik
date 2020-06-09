<?php

namespace YaFou\Mapik\Config\ConfigFunction;

use YaFou\Mapik\Kernel;

class PathFunction implements ConfigFunction
{
    private $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function call(array $arguments): string
    {
        switch ($arguments[0]) {
            case 'root':
                return $this->kernel->getRootDirectory();

            case 'config':
                return $this->kernel->getConfigDirectory();

            default:
                return '';
        }
    }
}
