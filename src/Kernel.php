<?php

namespace YaFou\Mapik;

use YaFou\Mapik\Exception\InvalidArgumentException;

class Kernel
{
    private $rootDirectory;

    public static function boot(string $rootDirectory): self
    {
        if(!file_exists($rootDirectory)) {
            throw new InvalidArgumentException(sprintf(
                'The directory "%s" does not exist', $rootDirectory
            ));
        }

        if(is_file($rootDirectory)) {
            throw new InvalidArgumentException(sprintf(
                'The root directory "%s" is a file',
                $rootDirectory
            ));
        }

        return new self($rootDirectory);
    }

    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
    }

    private function __construct(string $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }
}