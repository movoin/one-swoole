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

use One\Protocol\Factory;
use Psr\Http\Message\StreamInterface;

class StreamTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateStreamFromString()
    {
        $stream = Factory::newStream('test');
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $stream->close();
    }

    public function testCreateStreamFromResource()
    {
        $stream = Factory::newStream(fopen(RUNTIME_PATH . '/folder/test.txt', 'rw+'));
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $stream->close();
    }

    public function testCreateStreamFromStream()
    {
        $stream = Factory::newStream(fopen(RUNTIME_PATH . '/folder/test.txt', 'rw+'));
        $stream_ = Factory::newStream($stream);
        $this->assertInstanceOf(StreamInterface::class, $stream_);
        $stream->close();
        $stream_->close();
    }

    public function testDetach()
    {
        $stream = Factory::newStream(fopen(RUNTIME_PATH . '/folder/test.txt', 'rw+'));
        $this->assertTrue(is_resource($stream->detach()));
        $this->assertTrue(is_null($stream->detach()));
        $stream->close();
    }

    public function testGetSize()
    {
        $stream = Factory::newStream('test');

        $this->assertEquals(4, $stream->getSize());
        $this->assertEquals(4, $stream->getSize());

        $stream->detach();

        $this->assertEquals(null, $stream->getSize());
        $stream->close();
    }

    public function testTell()
    {
        $stream = Factory::newStream('test');

        $this->assertEquals(4, $stream->tell());
        $stream->close();
    }

    public function testEof()
    {
        $stream = Factory::newStream('test');

        $this->assertFalse($stream->eof());
        $stream->close();
    }

    public function testIsSeekable()
    {
        $stream = Factory::newStream('test');

        $this->assertTrue($stream->isSeekable());
        $stream->close();
    }

    public function testRewind()
    {
        $stream = Factory::newStream('test');

        $this->assertNull($stream->rewind());
        $stream->close();
    }

    public function testIsWritable()
    {
        $stream = Factory::newStream('test');

        $this->assertTrue($stream->isWritable());
        $stream->close();
    }

    public function testIsReadable()
    {
        $stream = Factory::newStream('test');

        $this->assertTrue($stream->isReadable());
        $stream->rewind();
        $this->assertEquals('te', $stream->read(2));
        $stream->close();
    }

    public function testToString()
    {
        $stream = Factory::newStream('test');

        $this->assertEquals("test", (string) $stream);
        $stream->detach();
        $this->assertEquals("", (string) $stream);
        $stream->close();
    }

    public function testGetMetadata()
    {
        $stream = Factory::newStream('test');
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
        Factory::newStream();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testReadException()
    {
        $stream = Factory::newStream(fopen(RUNTIME_PATH . '/folder/test.txt', 'rw'));
        $stream->read(filesize(RUNTIME_PATH . '/folder/test.txt'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testWriteException()
    {
        $stream = Factory::newStream(fopen(RUNTIME_PATH . '/folder/test.txt', 'r'));
        $stream->write('test');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSeekException()
    {
        $stream = Factory::newStream('test');
        $stream->seek(-10);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetContentsException()
    {
        $stream = Factory::newStream('test');
        $stream->detach();
        $stream->getContents();
    }
}
