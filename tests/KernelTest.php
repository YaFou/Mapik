<?php

namespace YaFou\Mapik\Tests;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use YaFou\Mapik\Exception\InvalidArgumentException;
use YaFou\Mapik\Kernel;

class KernelTest extends TestCase
{
    public function testNotFoundRootDirectory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The directory "root" does not exist');

        Kernel::boot('root');
    }

    public function testRootDirectoryIsAFile(): void
    {
        $fileSystem = vfsStream::setup('root', null, ['root' => '']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'The root directory "%s" is a file',
            $fileSystem->url() . '/root'
        ));

        Kernel::boot($fileSystem->url() . '/root');
    }
    
    public function testGetRootDirectory(): void
    {
        $fileSystem = vfsStream::setup();

        $this->assertSame(
            $fileSystem->url(),
            Kernel::boot($fileSystem->url())->getRootDirectory()
        );
    }
}
