<?php

namespace YaFou\Mapik\Config\ConfigFunction;

interface ConfigFunction
{
    public function call(array $arguments): string;
}
