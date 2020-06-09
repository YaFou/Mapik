<?php

namespace YaFou\Mapik;

use YaFou\Mapik\Config\Config;
use YaFou\Mapik\Config\Environment\Environment;
use YaFou\Mapik\Exception\InvalidArgumentException;

class Kernel
{
    private $rootDirectory;
    private $configDirectory;

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

        $kernel = new self($rootDirectory, $configDirectory);

        self::bootConfiguration(
            $rootDirectory . DIRECTORY_SEPARATOR . $configDirectory,
            $kernel,
            $environment
        );

        return $kernel;
    }

    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
    }

    public function getConfigDirectory(): string
    {
        return $this->configDirectory;
    }

    private static function bootEnvironment(string $rootDirectory): Environment
    {
        $environment = new Environment($rootDirectory);
        $environment->load();

        return $environment;
    }

    private static function bootConfiguration(
        string $configDirectory,
        self $kernel,
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

        $config = new Config($configDirectory, $kernel, $environment);
        $config->load();

        return $config;
    }

    private function __construct(string $rootDirectory, string $configDirectory)
    {
        $this->rootDirectory = $rootDirectory;
        $this->configDirectory = $configDirectory;
    }
}
