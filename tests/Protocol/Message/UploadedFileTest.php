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
use One\Protocol\Message\UploadedFile;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFileTest extends \PHPUnit\Framework\TestCase
{
    protected $file;

    public function setUp()
    {
        $this->file = Factory::newUploadedFile([
            'tmp_name'  => RUNTIME_PATH . '/word.docx',
            'size'      => filesize(RUNTIME_PATH . '/word.docx'),
            'error'     => UPLOAD_ERR_OK,
            'name'      => 'word.docx',
            'type'      => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }

    public function tearDown()
    {
        $this->file = null;
    }

    public function testGetStream()
    {
        $this->assertInstanceOf(StreamInterface::class, $this->file->getStream());
    }

    public function testGetSize()
    {
        $this->assertEquals(
            filesize(RUNTIME_PATH . '/word.docx'),
            $this->file->getSize()
        );
    }

    public function testGetError()
    {
        $this->assertEquals(
            UPLOAD_ERR_OK,
            $this->file->getError()
        );
    }

    public function testGerClientFilename()
    {
        $this->assertEquals('word.docx', $this->file->getClientFilename());
    }

    public function testGerClientMediaType()
    {
        $this->assertEquals(
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            $this->file->getClientMediaType()
        );
    }

    public function testFromResource()
    {
        $file = Factory::newUploadedFile([
            'tmp_name'  => fopen(RUNTIME_PATH . '/word.docx', 'r+w'),
            'size'      => filesize(RUNTIME_PATH . '/word.docx'),
            'error'     => UPLOAD_ERR_OK,
            'name'      => 'word.docx',
            'type'      => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);

        $this->assertInstanceOf(StreamInterface::class, $file->getStream());
    }

    public function testFromStream()
    {
        $file = Factory::newUploadedFile([
            'tmp_name'  => Factory::newStream(fopen(RUNTIME_PATH . '/word.docx', 'r+w')),
            'size'      => filesize(RUNTIME_PATH . '/word.docx'),
            'error'     => UPLOAD_ERR_OK,
            'name'      => 'word.docx',
            'type'      => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);

        $this->assertInstanceOf(StreamInterface::class, $file->getStream());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetStreamOrFileException()
    {
        Factory::newUploadedFile([
            'tmp_name'  => false,
            'size'      => filesize(RUNTIME_PATH . '/word.docx'),
            'error'     => UPLOAD_ERR_OK,
            'name'      => 'word.docx',
            'type'      => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetErrorException()
    {
        Factory::newUploadedFile([
            'tmp_name'  => Factory::newStream(fopen(RUNTIME_PATH . '/word.docx', 'r+w')),
            'size'      => filesize(RUNTIME_PATH . '/word.docx'),
            'error'     => UPLOAD_ERR_OK + 100,
            'name'      => 'word.docx',
            'type'      => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetClientFilenameException()
    {
        Factory::newUploadedFile([
            'tmp_name'  => Factory::newStream(fopen(RUNTIME_PATH . '/word.docx', 'r+w')),
            'size'      => filesize(RUNTIME_PATH . '/word.docx'),
            'error'     => UPLOAD_ERR_OK,
            'name'      => false,
            'type'      => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetClientMediaTypeException()
    {
        Factory::newUploadedFile([
            'tmp_name'  => Factory::newStream(fopen(RUNTIME_PATH . '/word.docx', 'r+w')),
            'size'      => filesize(RUNTIME_PATH . '/word.docx'),
            'error'     => UPLOAD_ERR_OK,
            'name'      => 'word.docx',
            'type'      => false
        ]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNotOkException()
    {
        $file = Factory::newUploadedFile([
            'tmp_name'  => Factory::newStream(fopen(RUNTIME_PATH . '/word.docx', 'r+w')),
            'size'      => filesize(RUNTIME_PATH . '/word.docx'),
            'error'     => UPLOAD_ERR_INI_SIZE,
            'name'      => 'word.docx',
            'type'      => 'word.docx'
        ]);

        $file->getStream();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMovedException()
    {
        if (file_exists(RUNTIME_PATH . '/test.txt')) {
            unlink(RUNTIME_PATH . '/test.txt');
        }
        if (file_exists(RUNTIME_PATH . '/test2.txt')) {
            unlink(RUNTIME_PATH . '/test2.txt');
        }

        file_put_contents(RUNTIME_PATH . '/test.txt', 'test');

        $file = Factory::newUploadedFile([
            'tmp_name'  => Factory::newStream(fopen(RUNTIME_PATH . '/test.txt', 'r+w')),
            'size'      => filesize(RUNTIME_PATH . '/test.txt'),
            'error'     => UPLOAD_ERR_OK,
            'name'      => 'test.txt',
            'type'      => 'plan/text'
        ]);

        $file->moveTo(RUNTIME_PATH . '/test2.txt');
        $file->moveTo(RUNTIME_PATH . '/test3.txt');
    }
}
