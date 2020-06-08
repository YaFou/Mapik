<?php

namespace YaFou\Mapik\Config;

use YaFou\Mapik\Config\Environment\Environment;

class Config
{
    private $configDirectory;
    private $environment;

    public function __construct(
        string $configDirectory,
        Environment $environment
    ) {
        $this->configDirectory = $configDirectory;
        $this->environment = $environment;
    }

    public function load(): void
    {
    }
}
