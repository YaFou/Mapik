<?php

namespace YaFou\Mapik\Tests\Config;

use Generator;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use YaFou\Mapik\Config\Config;
use YaFou\Mapik\Config\Environment\Environment;
use YaFou\Mapik\Exception\ConfigException;
use YaFou\Mapik\Kernel;

class ConfigTest extends TestCase
{
    private function makeConfig(array $files): Config
    {
        $environment = $this->createMock(Environment::class);
        $environment->method('get')->with('environment')->willReturn('env');

        $config = new Config(
            vfsStream::setup('root', null, $files)->url(),
            $this->createMock(Kernel::class),
            $environment
        );

        $config->load();

        return $config;
    }

    /**
     * @dataProvider provideInvalidFileNames
     *
     * @return void
     */
    public function testDoesNotLoadInvalidFiles(string $fileName): void
    {
        $this->assertNull(
            $this->makeConfig([$fileName => 'key: value'])->get('key')
        );
    }

    public function provideInvalidFileNames(): Generator
    {
        yield ['config.yal'];
        yield ['config.YALM'];
    }
    
    /**
     * @dataProvider provideFileNames
     *
     * @return void
     */
    public function testLoadAllFiles(string $fileName): void
    {
        $this->assertSame(
            'value',
            $this->makeConfig([$fileName => 'key: value'])->get('key')
        );
    }

    public function provideFileNames(): Generator
    {
        yield ['config.yaml'];
        yield ['config.yml'];
        yield ['config.YamL'];
        yield ['config.YML'];
    }

    public function testInvalidSyntaxException(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Unable to parse config files: ');

        $this->makeConfig(['config.yaml' => '*']);
    }

    public function testGetOnEnvironmentFile(): void
    {
        $this->assertSame(
            'value2',
            $this->makeConfig([
                'config.yaml' => 'key: value1',
                'config.env.yaml' => 'key: value2'
            ])->get('key')
        );
    }

    public function testGetValueInADirectory(): void
    {
        $this->assertSame('value', $this->makeConfig([
            'directory' => ['config.yaml' => 'key: value']
        ])->get('key'));
    }

    public function testGetFromFunctions(): void
    {
        $this->assertSame(
            'env',
            $this->makeConfig(['config.yaml' => "key: '%env(environment)%'"])
                ->get('key')
        );
    }
}
