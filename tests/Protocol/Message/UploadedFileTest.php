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

use One\Protocol\Message\UploadedFile;

class UploadedFileTest extends \PHPUnit\Framework\TestCase
{
    protected $file;

    public function setUp()
    {
        file_put_contents(RUNTIME_PATH . '/file.txt', 'file', LOCK_EX);

        $this->file = new UploadedFile(
            RUNTIME_PATH . '/file.txt',
            'file.txt',
            'plan/text',
            4,
            UPLOAD_ERR_OK
        );
    }

    public function tearDown()
    {
        if (file_exists(RUNTIME_PATH . '/file.txt')) {
            unlink(RUNTIME_PATH . '/file.txt');
        }

        if (file_exists(RUNTIME_PATH . '/moved.txt')) {
            unlink(RUNTIME_PATH . '/moved.txt');
        }

        $this->file = null;
    }

    public function testGetStream()
    {
        $this->assertInstanceOf(
            'Psr\\Http\\Message\\StreamInterface',
            $this->file->getStream()
        );
    }

    public function testMoveTo()
    {
        $this->file->moveTo(RUNTIME_PATH . '/moved.txt');
        $this->assertFileExists(RUNTIME_PATH . '/moved.txt');
    }

    /**
     * @dataProvider provideGetMethods
     */
    public function testGetMethods($method, $result)
    {
        $this->assertEquals($result, $this->file->$method());
    }

    public function provideGetMethods()
    {
        return [
            [ 'getError',           UPLOAD_ERR_OK ],
            [ 'getClientFilename',  'file.txt' ],
            [ 'getClientMediaType', 'plan/text' ],
            [ 'getSize',            4 ],
        ];
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAlreadyMovedException()
    {
        $this->file->moveTo(RUNTIME_PATH . '/moved.txt');
        $this->file->getStream();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAlreadyMovedException2()
    {
        $this->file->moveTo(RUNTIME_PATH . '/moved.txt');
        $this->file->moveTo(RUNTIME_PATH . '/moved.txt');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testErrorMoveingException()
    {
        $this->file->moveTo(RUNTIME_PATH . '/folder/');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPathNotWritableException()
    {
        $this->file->moveTo('/foo.txt');
    }
}
