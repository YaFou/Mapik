<?php

namespace YaFou\Mapik;

use YaFou\Mapik\Config\Config;
use YaFou\Mapik\Config\Environment\Environment;
use YaFou\Mapik\Exception\InvalidArgumentException;

class Kernel
{
    private $rootDirectory;

    public static function boot(
        string $rootDirectory,
        string $configDirectory = 'config'
    ): self {
        if (!file_exists($rootDirectory)) {
            throw new InvalidArgumentException(sprintf(
                'The directory "%s" does not exist',
                $rootDirectory
            ));
        }

        if (!is_dir($rootDirectory)) {
            throw new InvalidArgumentException(sprintf(
                'The root directory "%s" is not a directory',
                $rootDirectory
            ));
        }

        $environment = self::bootEnvironment($rootDirectory);
        self::bootConfiguration(
            $rootDirectory . DIRECTORY_SEPARATOR . $configDirectory,
            $environment
        );

        return new self($rootDirectory);
    }

    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
    }

    private static function bootEnvironment(string $rootDirectory): Environment
    {
        return new Environment($rootDirectory);
    }

    private static function bootConfiguration(
        string $configDirectory,
        Environment $environment
    ): Config {
        if (!file_exists($configDirectory)) {
            throw new InvalidArgumentException(sprintf(
                'The directory "%s" does not exist',
                $configDirectory
            ));
        }

        if (!is_dir($configDirectory)) {
            throw new InvalidArgumentException(sprintf(
                'The root directory "%s" is not a directory',
                $configDirectory
            ));
        }

        $config = new Config($configDirectory, $environment);
        $config->load();

        return $config;
    }

    private function __construct(string $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }
}
