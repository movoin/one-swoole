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

use One\FileSystem\Adapters\Local;

class LocalAdapterTest extends AdapterTestCase
{
    public function testExists()
    {
        $this->assertTrue($this->getAdapter()->exists('test.txt'));
    }

    public function testRead()
    {
        $this->assertEquals('test', $this->getAdapter()->read('test.txt'));
    }

    public function testReadStream()
    {
        $this->assertTrue(is_resource($this->getAdapter()->readStream('test.txt')));
    }

    public function testWriteStream()
    {
        $this->getAdapter()->writeStream(
            'test/test2.txt',
            $this->getAdapter()->readStream('test.txt'),
            ['visibility' => 'private']
        );

        $this->assertFileExists(RUNTIME_PATH . '/test/test2.txt');
        $this->assertEquals('test', $this->getAdapter()->read('test/test2.txt'));

        $this->getAdapter()->deleteDir('test');
    }

    public function testUpdate()
    {
        $this->getAdapter()->update('test.txt', 'update', ['visibility' => 'private']);
        $this->assertEquals('update', $this->getAdapter()->read('test.txt'));
    }

    public function testUpdateStream()
    {
        $this->getAdapter()->write('test2.txt', 'test2', ['visibility' => 'private']);
        $test2 = $this->getAdapter()->readStream('test2.txt');
        $this->getAdapter()->updateStream('test.txt', $test2, ['visibility' => 'private']);

        $this->assertEquals('test2', $this->getAdapter()->read('test.txt'));
        $this->getAdapter()->delete('test2.txt');
    }

    public function testGetMetaData()
    {
        $metadata = [
            'type' => 'file',
            'path' => 'word.docx'
        ];

        $meta = $this->getAdapter()->getMetaData('word.docx');

        $this->assertTrue(isset($meta['type']));
        $this->assertTrue(isset($meta['path']));
        $this->assertTrue(isset($meta['timestamp']));
        $this->assertTrue(isset($meta['size']));

        unset($meta['timestamp'], $meta['size']);

        $this->assertSame($metadata, $meta);
    }

    public function testGetMimeType()
    {
        $mimetype = $this->getAdapter()->getMimeType('excel.xlsx');

        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $mimetype
        );

        $mimetype = $this->getAdapter()->getMimeType('test.bin');

        $this->assertEquals('application/octet-stream', $mimetype);
    }

    public function testGetVisibility()
    {
        $this->assertEquals('public', $this->getAdapter()->getVisibility('test.txt'));
    }

    public function testSetVisibility()
    {
        $this->getAdapter()->setVisibility('test.txt', 'private');
        $this->assertEquals('private', $this->getAdapter()->getVisibility('test.txt'));
    }

    public function testRename()
    {
        $this->getAdapter()->rename('test.txt', 'test2.txt');

        $this->assertFalse($this->getAdapter()->exists('test.txt'));
        $this->assertTrue($this->getAdapter()->exists('test2.txt'));

        $this->getAdapter()->delete('test2.txt');
    }

    public function testCreateDirAndDeleteDir()
    {
        $this->assertTrue($this->getAdapter()->createDir('test1'));
        $this->assertTrue($this->getAdapter()->exists('test1'));
        $this->getAdapter()->deleteDir('test1');
        $this->assertFalse($this->getAdapter()->exists('test1'));
    }

    public function testDeleteRecursiveDir()
    {
        if ($this->getAdapter()->exists('test2')) {
            $this->getAdapter()->deleteDir('test2');
        }

        $this->getAdapter()->createDir('test2');
        $this->getAdapter()->createDir('test2/test3');
        $this->getAdapter()->createDir('test2/test3/test4');

        $this->getAdapter()->write('test2/t1.txt', 'test');
        $this->getAdapter()->write('test2/t2.txt', 'test');
        $this->getAdapter()->write('test2/test3/t1.txt', 'test');
        $this->getAdapter()->write('test2/test3/t2.txt', 'test');
        $this->getAdapter()->write('test2/test3/t3.txt', 'test');

        symlink(
            RUNTIME_PATH . '/excel.xlsx',
            RUNTIME_PATH . '/test2/test3/excel.link'
        );

        $this->getAdapter()->deleteDir('test2');
        $this->assertFalse($this->getAdapter()->exists('test2'));
    }

    public function testGetLinkMetaData()
    {
        $this->assertEmpty($this->getAdapter()->getMetaData('excel.link'));
    }
}
