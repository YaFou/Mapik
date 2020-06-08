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
            'The root directory "%s/root" is not a directory',
            $fileSystem->url()
        ));

        Kernel::boot($fileSystem->url() . '/root');
    }
    
    public function testGetRootDirectory(): void
    {
        $fileSystem = vfsStream::setup('root', null, ['config' => []]);

        $this->assertSame(
            $fileSystem->url(),
            Kernel::boot($fileSystem->url())->getRootDirectory()
        );
    }

    public function testNotFoundConfigDirectory(): void
    {
        $fileSystem = vfsStream::setup();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'The directory "%s%sconfig" does not exist',
            $fileSystem->url(),
            DIRECTORY_SEPARATOR
        ));

        Kernel::boot($fileSystem->url());
    }

    public function testConfigDirectoryIsAFile(): void
    {
        $fileSystem = vfsStream::setup('root', null, ['config' => '']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'The root directory "%s%sconfig" is not a directory',
            $fileSystem->url(),
            DIRECTORY_SEPARATOR
        ));

        Kernel::boot($fileSystem->url());
    }

    public function testCustomConfigDirectory(): void
    {
        $fileSystem = vfsStream::setup('root', null, ['custom_config' => []]);

        $this->assertInstanceOf(
            Kernel::class,
            Kernel::boot($fileSystem->url(), 'custom_config')
        );
    }
}
