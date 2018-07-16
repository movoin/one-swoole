<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Protocol\Message
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Protocol\Message;

use One\Protocol\Message\Stream;

class StreamTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateStreamFromString()
    {
        $stream = new Stream('test');
        $this->assertInstanceOf(Stream::class, $stream);
        $stream->close();
    }

    public function testCreateStreamFromResource()
    {
        $stream = new Stream(fopen(RUNTIME_PATH . '/folder/test.txt', 'rw+'));
        $this->assertInstanceOf(Stream::class, $stream);
        $stream->close();
    }

    public function testDetach()
    {
        $stream = new Stream(fopen(RUNTIME_PATH . '/folder/test.txt', 'rw+'));
        $this->assertTrue(is_resource($stream->detach()));
        $this->assertTrue(is_null($stream->detach()));
        $stream->close();
    }

    public function testGetSize()
    {
        $stream = new Stream('test');

        $this->assertEquals(4, $stream->getSize());
        $this->assertEquals(4, $stream->getSize());

        $stream->detach();

        $this->assertEquals(null, $stream->getSize());
        $stream->close();
    }

    public function testTell()
    {
        $stream = new Stream('test');

        $this->assertEquals(4, $stream->tell());
        $stream->close();
    }

    public function testEof()
    {
        $stream = new Stream('test');

        $this->assertFalse($stream->eof());
        $stream->close();
    }

    public function testIsSeekable()
    {
        $stream = new Stream('test');

        $this->assertTrue($stream->isSeekable());
        $stream->close();
    }

    public function testRewind()
    {
        $stream = new Stream('test');

        $this->assertNull($stream->rewind());
        $stream->close();
    }

    public function testIsWritable()
    {
        $stream = new Stream('test');

        $this->assertTrue($stream->isWritable());
        $stream->close();
    }

    public function testIsReadable()
    {
        $stream = new Stream('test');

        $this->assertTrue($stream->isReadable());
        $stream->rewind();
        $this->assertEquals('te', $stream->read(2));
        $stream->close();
    }

    public function testToString()
    {
        $stream = new Stream('test');

        $this->assertEquals("test", (string) $stream);
        $stream->close();
    }

    public function testGetMetadata()
    {
        $stream = new Stream('test');
        $meta = $stream->getMetadata();

        $this->assertEquals("php://temp", $meta['uri']);

        $stream->detach();
        $this->assertEquals([], $stream->getMetadata());
        $stream->close();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructException()
    {
        new Stream('');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testReadException()
    {
        $stream = new Stream(fopen(RUNTIME_PATH . '/folder/test.txt', 'w'));
        $stream->read(filesize(RUNTIME_PATH . '/folder/test.txt'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testWriteException()
    {
        $stream = new Stream(fopen(RUNTIME_PATH . '/folder/test.txt', 'r'));
        $stream->write('test');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSeekException()
    {
        $stream = new Stream('test');
        $stream->seek(-10);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetContentsException()
    {
        $stream = new Stream('test');
        $stream->detach();
        $stream->getContents();
    }
}
