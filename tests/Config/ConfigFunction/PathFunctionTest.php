<?php

namespace YaFou\Mapik\Tests\Config\ConfigFunction;

use PHPUnit\Framework\TestCase;
use YaFou\Mapik\Config\ConfigFunction\PathFunction;
use YaFou\Mapik\Kernel;

class PathFunctionTest extends TestCase
{
    public function testGetRootDirectory(): void
    {
        $kernel = $this->createMock(Kernel::class);
        $kernel->expects($this->once())
            ->method('getRootDirectory')
            ->willReturn('root');

        $this->assertSame(
            'root',
            (new PathFunction($kernel))->call(['root'])
        );
    }

    public function testGetConfigDirectory(): void
    {
        $kernel = $this->createMock(Kernel::class);
        $kernel->expects($this->once())
            ->method('getConfigDirectory')
            ->willReturn('config');

        $this->assertSame(
            'config',
            (new PathFunction($kernel))->call(['config'])
        );
    }

    public function testGetWrongPath(): void
    {
        $this->assertSame(
            '',
            (new PathFunction($this->createMock(Kernel::class)))
                ->call(['wrong'])
        );
    }
}
