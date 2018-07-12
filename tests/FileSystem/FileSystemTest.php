<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\FileSystem
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\FileSystem;

use One\FileSystem\FileSystem;
use One\FileSystem\Adapters\Local;

class FileSystemTest extends \PHPUnit\Framework\TestCase
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

    public function testGetAdapter()
    {
        $this->assertInstanceOf(
            'One\\FileSystem\\Adapters\\Local',
            $this->fs->getAdapter()
        );
    }

    public function testExists()
    {
        $this->assertTrue($this->fs->exists('word.docx'));
    }

    public function testRead()
    {
        $this->assertEquals('test', $this->fs->read('test.txt'));
    }

    public function testReadStream()
    {
        $this->assertTrue(is_resource($this->fs->readStream('test.txt')));
    }

    public function testReadAndDelete()
    {
        $this->assertEquals('test', $this->fs->readAndDelete('test.txt'));
        $this->assertFalse($this->fs->exists('test.txt'));
    }

    public function testWriteStream()
    {
        $file = fopen(RUNTIME_PATH . '/test.txt', 'rb');
        $this->assertTrue($this->fs->writeStream('test_stream.txt', $file));
        $this->assertTrue($this->fs->exists('test_stream.txt'));
        $this->fs->delete('test_stream.txt');
    }

    public function testUpdate()
    {
        $this->assertTrue($this->fs->update('test.txt', 'update'));
        $this->assertEquals('update', $this->fs->read('test.txt'));

        $this->fs->update('test.txt', 'test');
    }

    public function testUpdateStream()
    {
        $this->fs->write('test2.txt', 'test2');

        $file = fopen(RUNTIME_PATH . '/test2.txt', 'rb');
        $this->assertTrue($this->fs->updateStream('test.txt', $file));
        $this->assertEquals('test2', $this->fs->read('test.txt'));

        $this->fs->update('test.txt', 'test');
        $this->fs->delete('test2.txt');
    }

    public function testPut()
    {
        $this->assertTrue($this->fs->put('test2.txt', 'test2'));
        $this->assertEquals('test2', $this->fs->read('test2.txt'));

        $this->assertTrue($this->fs->put('test2.txt', 'update'));
        $this->assertEquals('update', $this->fs->read('test2.txt'));

        $this->fs->delete('test2.txt');
    }

    public function testPutStream()
    {
        $this->fs->write('test_put1.txt', 'test_a');
        $this->fs->write('test_put2.txt', 'test_b');

        $file1 = fopen(RUNTIME_PATH . '/test_put1.txt', 'rb');
        $file2 = fopen(RUNTIME_PATH . '/test_put2.txt', 'rb');

        $this->assertTrue($this->fs->putStream('test2.txt', $file1));
        $this->assertEquals('test_a', $this->fs->read('test2.txt'));

        $this->assertTrue($this->fs->putStream('test2.txt', $file2));
        $this->assertEquals('test_b', $this->fs->read('test2.txt'));

        $this->fs->delete('test_put1.txt');
        $this->fs->delete('test_put2.txt');
        $this->fs->delete('test2.txt');
    }

    public function testRename()
    {
        $this->fs->write('test_rename.txt', 'test');

        $this->assertTrue($this->fs->rename('test_rename.txt', 'test_renamed.txt'));
        $this->assertTrue($this->fs->exists('test_renamed.txt'));

        $this->fs->delete('test_renamed.txt');
    }

    public function testCreateAndDeleteDir()
    {
        $this->assertTrue($this->fs->createDir('newdir'));
        $this->assertTrue($this->fs->deleteDir('newdir'));
    }

    public function testGetMimeType()
    {
        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            $this->fs->getMimeType('word.docx')
        );
    }

    public function testGetMetaData()
    {
        $metadata = [
            'type' => 'file',
            'path' => 'word.docx'
        ];

        $meta = $this->fs->getMetaData('word.docx');

        $this->assertTrue(isset($meta['type']));
        $this->assertTrue(isset($meta['path']));
        $this->assertTrue(isset($meta['timestamp']));
        $this->assertTrue(isset($meta['size']));

        unset($meta['timestamp'], $meta['size']);

        $this->assertSame($metadata, $meta);
    }

    public function testGetVisibility()
    {
        $this->assertEquals('public', $this->fs->getVisibility('word.docx'));
    }

    public function testNormalizePath()
    {
        $this->assertEquals('test', $this->fs->read('./test.txt'));
        $this->assertEquals('test', $this->fs->read('/./test.txt'));
        $this->assertEquals('test', $this->fs->read('test/../test.txt'));
    }
}
