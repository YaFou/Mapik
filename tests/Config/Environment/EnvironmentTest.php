<?php

namespace YaFou\Mapik\Tests\Config\Environment;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use YaFou\Mapik\Exception\LogicException;
use YaFou\Mapik\Config\Environment\Environment;
use YaFou\Mapik\Exception\EnvironmentException;

class EnvironmentTest extends TestCase
{
    private function makeEnvironment(
        string $master,
        array $environmentFiles = []
    ): Environment {
        $structure = ['.env' => $master];

        foreach ($environmentFiles as $name => $content) {
            $structure[sprintf('.env.%s', $name)] = $content;
        }

        $environment = new Environment(
            vfsStream::setup('root', null, $structure)->url()
        );

        $environment->load();

        return $environment;
    }

    public function testGetOnMasterFile(): void
    {
        $this->assertSame(
            'value',
            $this->makeEnvironment('ENVIRONMENT=value')->get('environment')
        );
    }

    public function testGetBeforeLoading(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'The environment must be loaded before getting a value'
        );

        (new Environment(
            vfsStream::setup('root', null, ['.env' => 'KEY=value'])->url()
        ))->get('key');
    }

    public function testLoadWithoutMasterEnvironmentFile(): void
    {
        $fileSystem = vfsStream::setup();

        $this->expectException(EnvironmentException::class);
        $this->expectExceptionMessage(sprintf(
            'The file "%s%s.env" was not found',
            $fileSystem->url(),
            DIRECTORY_SEPARATOR
        ));

        (new Environment($fileSystem->url()))->load();
    }

    public function testLoadWithAMasterEnvironmentAsADirectory(): void
    {
        $fileSystem = vfsStream::setup('root', [], ['.env' => []]);

        $this->expectException(EnvironmentException::class);
        $this->expectExceptionMessage(sprintf(
            'The file "%s%s.env" is not a file',
            $fileSystem->url(),
            DIRECTORY_SEPARATOR
        ));

        (new Environment($fileSystem->url()))->load();
    }

    public function testEnvironmentKeyDoesNotExist(): void
    {
        $this->expectException(EnvironmentException::class);
        $this->expectExceptionMessage(
            'The master environment must have an "ENVIRONMENT" key'
        );

        $this->makeEnvironment('');
    }

    public function testEnvironmentKeyIsNull(): void
    {
        $this->expectException(EnvironmentException::class);
        $this->expectExceptionMessage(
            'The "ENVIRONMENT" key mustn\'t be null or empty'
        );

        $this->makeEnvironment('ENVIRONMENT');
    }

    public function testEnvironmentKeyIsEmpty(): void
    {
        $this->expectException(EnvironmentException::class);
        $this->expectExceptionMessage(
            'The "ENVIRONMENT" key mustn\'t be null or empty'
        );

        $this->makeEnvironment('ENVIRONMENT=');
    }

    public function testGetInOtherEnvironmentFile(): void
    {
        $this->assertSame(
            'value',
            $this->makeEnvironment(
                'ENVIRONMENT=env',
                ['env' => 'KEY=value']
            )->get('key')
        );
    }

    public function testOtherEnvironmentFileIsADirectory(): void
    {
        $fileSystem = vfsStream::setup('root', [], [
            '.env' => 'ENVIRONMENT=env',
            '.env.env' => []
        ]);

        $this->expectException(EnvironmentException::class);
        $this->expectExceptionMessage(sprintf(
            'The file "%s%s.env.env" is not a file',
            $fileSystem->url(),
            DIRECTORY_SEPARATOR
        ));
        
        (new Environment($fileSystem->url()))->load();
    }
}
