<?php

namespace YaFou\Mapik\Config\ConfigFunction;

use YaFou\Mapik\Config\Environment\Environment;

class EnvironmentFunction implements ConfigFunction
{
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function call(array $arguments): string
    {
        return $this->environment->get($arguments[0]);
    }
}
