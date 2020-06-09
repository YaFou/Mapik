<?php

namespace YaFou\Mapik\Config;

use Noodlehaus\Config as BaseConfig;
use Noodlehaus\Exception\ParseException;
use Noodlehaus\Parser\Yaml;
use YaFou\Mapik\Config\ConfigFunction\EnvironmentFunction;
use YaFou\Mapik\Config\ConfigFunction\PathFunction;
use YaFou\Mapik\Config\Environment\Environment;
use YaFou\Mapik\Exception\ConfigException;
use YaFou\Mapik\Kernel;

class Config
{
    private $configDirectory;
    private $environment;
    private $config;
    private $functions;

    public function __construct(
        string $configDirectory,
        Kernel $kernel,
        Environment $environment
    ) {
        $this->configDirectory = $configDirectory;
        $this->environment = $environment;

        $this->functions = [
            'env' => new EnvironmentFunction($this->environment),
            'path' => new PathFunction($kernel)
        ];
    }

    public function load(): void
    {
        try {
            $this->config = BaseConfig::load(
                $this->listFiles($this->configDirectory),
                new Yaml()
            );

            $this->config->merge(BaseConfig::load($this->listFiles(
                $this->configDirectory,
                $this->environment->get('environment')
            )));
        } catch (ParseException $e) {
            throw new ConfigException(sprintf(
                'Unable to parse config files: %s',
                $e->getMessage()
            ));
        }

        foreach ($this->config->all() as $key => $value) {
            preg_match_all(
                '/%(?<name>[a-z]+)\((?<arguments>[^)]+)\)%/',
                $value,
                $matches
            );

            array_shift($matches);

            if (empty($matches[0])) {
                continue;
            }

            $functions = [];

            for ($i = 0; $i < count($matches[0]); $i++) {
                $functions[] = [
                    'name' => $matches['name'][$i],
                    'arguments' => $matches['arguments'][$i]
                ];
            }

            $this->config->set($key, $this->resolveValue($value, $functions));
        }
    }

    public function get(string $key)
    {
        return $this->config[$key];
    }

    private function listFiles(
        string $directory,
        string $environment = null
    ): array {
        $pattern = null === $environment ?
            '/^[^\.]+\.(yaml|yml)$/i' :
            sprintf('/^[^\.]+\.%s\.(yaml|yml)$/i', $environment);
        
        $files = [];
        
        foreach (scandir($directory) as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            $filePath = sprintf(
                '%s%s%s',
                $directory,
                DIRECTORY_SEPARATOR,
                $file
            );

            if (is_file($filePath)) {
                if (preg_match($pattern, $file)) {
                    $files[] = $filePath;
                }
            }

            if (is_dir($filePath)) {
                $files = array_merge($files, $this->listFiles($filePath));
            }
        }

        return $files;
    }

    private function resolveValue(string $value, array $functions): string
    {
        foreach ($functions as $function) {
            $name = $function['name'];
            $arguments = $function['arguments'];

            $replace = $this->functions[$name]->call(array_map(
                function ($value) {
                    return trim($value);
                },
                explode(',', $arguments)
            ));
            
            $value = str_replace(
                sprintf('%%%s(%s)%%', $name, $arguments),
                $replace,
                $value
            );
        }

        return $value;
    }
}
