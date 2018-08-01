<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Message
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Message;

use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    /**
     * 文件路径
     *
     * @var string
     */
    protected $file;
    /**
     * 文件名称
     *
     * @var string
     */
    protected $name;
    /**
     * 文件类型
     *
     * @var string
     */
    protected $type;
    /**
     * 文件大小
     *
     * @var int
     */
    protected $size;
    /**
     * 文件上传错误码
     *
     * @var int
     */
    protected $error = UPLOAD_ERR_OK;
    /**
     * 上传文件流
     *
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $stream;
    /**
     * 文件移动标识
     *
     * @var bool
     */
    protected $moved = false;

    /**
     * 构造
     *
     * @param string        $file
     * @param string|null   $name
     * @param string|null   $type
     * @param int|null      $size
     * @param int           $error
     */
    public function __construct(
        $file,
        $name = null,
        $type = null,
        $size = null,
        $error = UPLOAD_ERR_OK
    ) {
        $this->file = $file;
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->error = $error;
    }

    /**
     * 获得上传文件流
     *
     * @return \Psr\Http\Message\StreamInterface
     * @throws \RuntimeException
     */
    public function getStream(): StreamInterface
    {
        if ($this->moved) {
            throw new RuntimeException('Uploaded file already moved');
        }

        if ($this->stream === null) {
            $this->stream = new Stream(fopen($this->file, 'r'));
        }

        return $this->stream;
    }

    /**
     * 移动上传文件至目标路径
     *
     * @param  string $targetPath
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function moveTo($targetPath)
    {
        if ($this->moved) {
            throw new RuntimeException('Uploaded file already moved');
        }

        if (! is_writable(dirname($targetPath))) {
            throw new InvalidArgumentException('Upload target path is not writable');
        }

        if (! @rename($this->file, $targetPath)) {
            throw new RuntimeException(
                sprintf('Error moving uploaded file %1s to %2s', $this->name, $targetPath)
            );
        }

        $this->moved = true;
    }

    /**
     * 获得上传错误码
     *
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * 获得客户端文件名
     *
     * @return null|string
     */
    public function getClientFilename()
    {
        return $this->name;
    }

    /**
     * 获得客户端文件类型
     *
     * @return null|string
     */
    public function getClientMediaType()
    {
        return $this->type;
    }

    /**
     * 获得文件大小
     *
     * @return null|int
     */
    public function getSize()
    {
        return $this->size;
    }
}
