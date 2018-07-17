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
use One\Support\Helpers\Assert;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    /**
     * 错误类型
     *
     * @var int[]
     */
    private static $errors = [
        UPLOAD_ERR_OK, UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE, UPLOAD_ERR_PARTIAL,
        UPLOAD_ERR_NO_FILE, UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION
    ];

    /**
     * @var string
     */
    private $clientFilename;
    /**
     * @var string
     */
    private $clientMediaType;
    /**
     * @var int
     */
    private $error;
    /**
     * @var string|null
     */
    private $file;
    /**
     * @var bool
     */
    private $moved = false;
    /**
     * @var int|null
     */
    private $size;
    /**
     * @var \Psr\Http\Message\StreamInterface|null
     */
    private $stream;

    /**
     * 构造
     *
     * @param \Psr\Http\Message\StreamInterface|string|resource $streamOrFile
     * @param int                                               $size
     * @param int                                               $errorStatus
     * @param string|null                                       $clientFilename
     * @param string|null                                       $clientMediaType
     */
    public function __construct(
        $streamOrFile,
        int $size,
        int $error,
        $clientFilename,
        $clientMediaType
    ) {
        $this->setSize($size);
        $this->setError($error);
        $this->setClientFilename($clientFilename);
        $this->setClientMediaType($clientMediaType);

        if ($this->isOk()) {
            $this->setStreamOrFile($streamOrFile);
        }
    }

    /**
     * 获得上传文件流
     *
     * @return Psr\Http\Message\StreamInterface
     * @throws \RuntimeException
     */
    public function getStream(): StreamInterface
    {
        $this->validateActive();

        if (Assert::instanceOf($this->stream, StreamInterface::class)) {
            return $this->stream;
        }

        $resource = fopen($this->file, 'r');

        return new Stream($resource);
    }

    /**
     * 将上传文件移动到目标路径
     *
     * @param  string $targetPath
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function moveTo($targetPath)
    {
        $this->validateActive();

        if (! Assert::stringNotEmpty($targetPath)) {
            throw new InvalidArgumentException('Parameter 1 must be the not empty string');
        }

        if (null !== $this->file) {
            $this->moved = @rename($this->file, $targetPath);
        } else {
            $source = $this->getStream();
            $dest   = new Stream(fopen($targetPath, 'w'));

            if ($source ->isSeekable()) {
                $source->rewind();
            }

            while (! $source->eof()) {
                if (! $dest->write($source->read(1048576))) {
                    break;
                }
            }

            if ($uri = $source->getMetadata('uri')) {
                @unlink($uri);
            }

            $this->moved = true;

            unset($source, $dest);
        }

        if (false === $this->moved) {
            throw new RuntimeException(
                sprintf(
                    'The uploaded file cannot be moved to the path "%s"',
                    $targetPath
                )
            );
        }
    }

    /**
     * 获得文件大小
     *
     * @return int|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * 获得文件上传的错误
     *
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 获得客户端文件名
     *
     * @return string|null
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * 获得客户端文件类型
     *
     * @return string|null
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

    /**
     * 设置文件流或文件
     *
     * @param mixed $streamOrFile
     *
     * @throws \InvalidArgumentException
     */
    private function setStreamOrFile($streamOrFile)
    {
        if (Assert::string($streamOrFile)) {
            $this->file = $streamOrFile;
        } elseif (Assert::resource($streamOrFile)) {
            $this->stream = new Stream($streamOrFile);
        } elseif ($streamOrFile instanceof StreamInterface) {
            $this->stream = $streamOrFile;
        } else {
            throw new InvalidArgumentException('Invalid data stream or file');
        }
    }

    /**
     * 设置错误
     *
     * @param int $error
     *
     * @throws \InvalidArgumentException
     */
    private function setError(int $error)
    {
        if (! Assert::oneOf($error, self::$errors)) {
            throw new InvalidArgumentException('Invalid error');
        }

        $this->error = $error;
    }

    /**
     * 设置上传文件大小
     *
     * @param int $size
     */
    private function setSize(int $size)
    {
        $this->size = $size;
    }

    /**
     * 设置上传文件名称
     *
     * @param string|null $filename
     *
     * @throws \InvalidArgumentException
     */
    private function setClientFilename($filename)
    {
        if (! Assert::oneOf(gettype($filename), ['string', 'NULL'])) {
            throw new InvalidArgumentException('Parameter 1 must be the `null` or `string`');
        }

        $this->clientFilename = $filename;
    }

    /**
     * 设置上传文件类型
     *
     * @param string|null $mediaType
     */
    private function setClientMediaType($mediaType)
    {
        if (! Assert::oneOf(gettype($mediaType), ['string', 'NULL'])) {
            throw new InvalidArgumentException('Parameter 1 must be the `null` or `string`');
        }

        $this->clientMediaType = $mediaType;
    }

    /**
     * 判断上传文件是否无错
     *
     * @return bool
     */
    private function isOk(): bool
    {
        return $this->error === UPLOAD_ERR_OK;
    }

    /**
     * 检查是否有效
     *
     * @throws \RuntimeException
     */
    private function validateActive()
    {
        if (false === $this->isOk()) {
            throw new RuntimeException('File upload error');
        }

        if ($this->moved) {
            throw new RuntimeException('The file has been moved');
        }
    }
}
