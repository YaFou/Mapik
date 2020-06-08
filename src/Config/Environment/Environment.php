<?php

namespace YaFou\Mapik\Config\Environment;

use Dotenv\Dotenv;
use YaFou\Mapik\Exception\EnvironmentException;
use YaFou\Mapik\Exception\LogicException;

class Environment
{
    private const ENVIRONMENT_KEY = 'environment';

    private $rootDirectory;
    private $values;

    public function __construct(string $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function load(): void
    {
        if (
            !file_exists(sprintf(
                '%s%s.env',
                $this->rootDirectory,
                DIRECTORY_SEPARATOR
            ))
        ) {
            throw new EnvironmentException(sprintf(
                'The file "%s%s.env" was not found',
                $this->rootDirectory,
                DIRECTORY_SEPARATOR
            ));
        }

        if (
            !is_file(sprintf(
                '%s%s.env',
                $this->rootDirectory,
                DIRECTORY_SEPARATOR
            ))
        ) {
            throw new EnvironmentException(sprintf(
                'The file "%s%s.env" is not a file',
                $this->rootDirectory,
                DIRECTORY_SEPARATOR
            ));
        }

        $values = Dotenv::createMutable($this->rootDirectory)->load();
        $this->values = [];

        foreach ($values as $key => $value) {
            $this->values[strtolower($key)] = $value;
        }

        if (!key_exists(self::ENVIRONMENT_KEY, $this->values)) {
            throw new EnvironmentException(sprintf(
                'The master environment must have an "%s" key',
                strtoupper(self::ENVIRONMENT_KEY)
            ));
        }

        $environment = $this->get('environment');
        
        if (
            null === $this->get(self::ENVIRONMENT_KEY) ||
            '' === $this->get('environment')
        ) {
            throw new EnvironmentException(sprintf(
                'The "%s" key mustn\'t be null or empty',
                strtoupper(self::ENVIRONMENT_KEY)
            ));
        }

        $this->loadEnvironmentFile($environment);
    }

    private function loadEnvironmentFile(string $environment): void
    {
        $environment = strtolower($environment);

        if (
            !file_exists(sprintf(
                '%s%s.env.%s',
                $this->rootDirectory,
                DIRECTORY_SEPARATOR,
                $environment
            ))
        ) {
            return;
        }

        if (
            !is_file(sprintf(
                '%s%s.env.%s',
                $this->rootDirectory,
                DIRECTORY_SEPARATOR,
                $environment
            ))
        ) {
            throw new EnvironmentException(sprintf(
                'The file "%s%s.env.%s" is not a file',
                $this->rootDirectory,
                DIRECTORY_SEPARATOR,
                $environment
            ));
        }
        
        $values = Dotenv::createMutable(
            $this->rootDirectory,
            sprintf('.env.%s', $environment)
        )->load();

        foreach ($values as $key => $value) {
            $this->values[strtolower($key)] = $value;
        }
    }

    public function get(string $key): ?string
    {
        if (null === $this->values) {
            throw new LogicException(
                'The environment must be loaded before getting a value'
            );
        }

        return $this->values[$key];
    }
}
