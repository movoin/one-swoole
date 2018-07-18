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
    protected $stream;

    public function setUp()
    {
        file_put_contents(RUNTIME_PATH . '/stream.txt', 'stream', LOCK_EX);
        $this->stream = new Stream(fopen(RUNTIME_PATH . '/stream.txt', 'r+'));
    }

    public function tearDown()
    {
        if (file_exists(RUNTIME_PATH . '/stream.txt')) {
            unlink(RUNTIME_PATH . '/stream.txt');
        }

        $this->stream->close();
        $this->stream = null;
    }

    public function testGetSize()
    {
        $this->assertEquals(6, $this->stream->getSize());
    }

    public function testTell()
    {
        $this->assertEquals(0, $this->stream->tell());
    }

    public function testEof()
    {
        $this->assertFalse($this->stream->eof());
    }

    public function testIsReadable()
    {
        $this->assertTrue($this->stream->isReadable());
    }

    public function testIsWritable()
    {
        $this->assertTrue($this->stream->isWritable());
    }

    public function testIsSeekable()
    {
        $this->assertTrue($this->stream->isSeekable());
    }

    public function testSeekAndRewind()
    {
        $this->stream->seek(3);
        $this->assertEquals(3, $this->stream->tell(), 'Seek');
        $this->stream->rewind();
        $this->assertEquals(0, $this->stream->tell(), 'Rewind');
    }

    public function testRead()
    {
        $this->assertEquals('str', $this->stream->read(3));
    }

    public function testWrite()
    {
        $this->stream->write('streamstream');
        $this->stream->rewind();
        $this->assertEquals('streamstr', $this->stream->read(9));
    }

    public function testGetMetadata()
    {
        $this->assertEquals(
            RUNTIME_PATH . '/stream.txt',
            $this->stream->getMetadata('uri')
        );
        $this->assertNull($this->stream->getMetadata('foo'));
    }

    public function testToString()
    {
        $this->assertEquals('stream', (string) $this->stream);
        $this->stream->close();
        $this->assertEquals('', (string) $this->stream);

        $stream = new Stream(fopen(RUNTIME_PATH . '/stream.txt', 'w'));
        $this->assertEquals('', (string) $stream);
    }

    /**
     * @dataProvider provideRuntimeExceptions
     * @expectedException \RuntimeException
     */
    public function testRuntimeExceptions($method, $params)
    {
        $this->stream->detach();
        call_user_func_array([$this->stream, $method], $params);
    }

    public function provideRuntimeExceptions()
    {
        return [
            [ 'tell', [] ],
            [ 'seek', [ 0 ] ],
            [ 'rewind', [] ],
            [ 'read', [ 1 ] ],
            [ 'write', [ 1 ] ],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAttachException()
    {
        new Stream('hello');
    }
}
