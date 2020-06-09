<?php

namespace YaFou\Mapik\Tests\Config\ConfigFunction;

use PHPUnit\Framework\TestCase;
use YaFou\Mapik\Config\ConfigFunction\EnvironmentFunction;
use YaFou\Mapik\Config\Environment\Environment;

class EnvironmentFunctionTest extends TestCase
{
    public function testCall(): void
    {
        $environment = $this->createMock(Environment::class);
        $environment->expects($this->once())
            ->method('get')
            ->willReturn('value');

        $this->assertSame(
            'value',
            (new EnvironmentFunction($environment))->call(['key'])
        );
    }
}
