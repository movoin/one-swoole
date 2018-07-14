<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\FileSystem\Exceptions
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\FileSystem\Exceptions;

use One\FileSystem\FileSystem;
use One\FileSystem\Adapters\Local;
use One\FileSystem\Exceptions\FileSystemException;

class FileSystemExceptionTest extends \PHPUnit\Framework\TestCase
{
    protected $fs;

    public function setUp()
    {
        $this->fs = new FileSystem(new Local(RUNTIME_PATH));
        $this->fs->write('test.txt', 'test');
    }

    public function tearDown()
    {
        if ($this->fs->exists('test.txt')) {
            $this->fs->delete('test.txt');
        }

        $this->fs = null;
    }

    /**
     * @expectedException One\FileSystem\Exceptions\FileNotExistsException
     */
    public function testAssertPresentException()
    {
        $this->fs->read('bad.txt');
    }

    /**
     * @expectedException One\FileSystem\Exceptions\FileExistsException
     */
    public function testAssertAbsentException()
    {
        $this->fs->write('test.txt', 'update');
    }

    /**
     * @expectedException One\FileSystem\Exceptions\DirectoryNotExistsException
     */
    public function testDeleteDirException()
    {
        $this->fs->deleteDir('');
    }

    /**
     * @expectedException One\FileSystem\Exceptions\FileSystemException
     */
    public function testNormalizePathException()
    {
        $this->fs->read('../test.txt');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testStreamException()
    {
        $this->fs->writeStream('test2.txt', '');
    }

    public function testStreamException2()
    {
        try {
            $this->fs->read('../test.txt');
        } catch (FileSystemException $e) {
            $this->assertEquals('../test.txt', $e->getPath());
        }
    }
}
