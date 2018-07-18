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

class Stream implements StreamInterface
{
    /**
     * 读写权限映射
     *
     * @var array
     */
    private static $modes = [
        'readable' => ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        'writable' => ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'],
    ];

    /**
     * 文件流对象
     *
     * @var resource
     */
    private $stream;
    /**
     * 是否可定位
     *
     * @var bool
     */
    private $seekable;
    /**
     * 是否可读取
     *
     * @var bool
     */
    private $readable;
    /**
     * 是否可写入
     *
     * @var bool
     */
    private $writable;
    /**
     * 文件流大小
     *
     * @var int
     */
    private $size;

    /**
     * 构造
     *
     * @param resource $resource
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($resource)
    {
        $this->attach($resource);
    }

    /**
     * 析构
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * 关闭流
     */
    public function close()
    {
        if ($this->isAttached() === true) {
            fclose($this->stream);
        }

        $this->detach();
    }

    /**
     * 分离资源
     *
     * @return resource|null
     */
    public function detach()
    {
        $stream = $this->stream;

        $this->stream = null;
        $this->readable = null;
        $this->writable = null;
        $this->seekable = null;
        $this->size = null;

        return $stream;
    }

    /**
     * 获得流的大小
     *
     * @return int|null
     */
    public function getSize()
    {
        if (! $this->size && $this->isAttached() === true) {
            $stats = fstat($this->stream);
            $this->size = isset($stats['size']) ? $stats['size'] : null;
        }

        return $this->size;
    }

    /**
     * 获得文件指针读/写的位置
     *
     * @return int
     * @throws \RuntimeException
     */
    public function tell(): int
    {
        if ($this->isAttached() === false || ($position = ftell($this->stream)) === false) {
            throw new RuntimeException('Could not get the position of the pointer in stream');
        }

        return $position;
    }

    /**
     * 获得数据流是否结束
     *
     * @return bool
     */
    public function eof(): bool
    {
        return $this->isAttached() === true ? feof($this->stream) : true;
    }

    /**
     * 判断文件流是否可读
     *
     * @return bool
     */
    public function isReadable(): bool
    {
        if ($this->readable === null) {
            $this->readable = false;
            if ($this->isAttached() === true) {
                $meta = $this->getMetadata();
                foreach (self::$modes['readable'] as $mode) {
                    if (strpos($meta['mode'], $mode) === 0) {
                        $this->readable = true;
                        break;
                    }
                }
            }
        }

        return $this->readable;
    }

    /**
     * 判断文件流是否可写
     *
     * @return bool
     */
    public function isWritable(): bool
    {
        if ($this->writable === null) {
            $this->writable = false;
            if ($this->isAttached() === true) {
                $meta = $this->getMetadata();
                foreach (self::$modes['writable'] as $mode) {
                    if (strpos($meta['mode'], $mode) === 0) {
                        $this->writable = true;
                        break;
                    }
                }
            }
        }

        return $this->writable;
    }

    /**
     * 判断流是否可定位
     *
     * @return bool
     */
    public function isSeekable(): bool
    {
        if ($this->seekable === null) {
            $this->seekable = false;
            if ($this->isAttached() === true) {
                $meta = $this->getMetadata();
                $this->seekable = $meta['seekable'];
            }
        }

        return $this->seekable;
    }

    /**
     * 获得流中的指定位置
     *
     * @param  int    $offset
     * @param  int    $whence
     *
     * @throws \RuntimeException
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if ($this->isSeekable() === false || fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException('Could not seek in stream');
        }
    }

    /**
     * 重新定义至流的开始处
     *
     * @throws \RuntimeException
     */
    public function rewind()
    {
        if ($this->isSeekable() === false || rewind($this->stream) === false) {
            throw new RuntimeException('Could not rewind stream');
        }
    }

    /**
     * 读取流内容
     *
     * @param  int    $length
     *
     * @return string
     * @throws \RuntimeException
     */
    public function read($length): string
    {
        if ($this->isReadable() === false || ($data = fread($this->stream, $length)) === false) {
            throw new RuntimeException('Could not read from stream');
        }

        return $data;
    }

    /**
     * 写入内容
     *
     * @param  string $string
     *
     * @return int
     * @throws \RuntimeException
     */
    public function write($string): int
    {
        if ($this->isWritable() === false || ($written = fwrite($this->stream, $string)) === false) {
            throw new RuntimeException('Could not write to stream');
        }

        $this->size = null;

        return $written;
    }

    /**
     * 获得数据内容
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getContents(): string
    {
        if ($this->isReadable() === false || ($contents = stream_get_contents($this->stream)) === false) {
            throw new RuntimeException('Could not get contents of stream');
        }

        return $contents;
    }

    /**
     * 获得源数据
     *
     * @param  string|null $key
     *
     * @return array|mixed|null
     */
    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->stream);

        if (is_null($key) === true) {
            return $meta;
        }

        return isset($meta[$key]) ? $meta[$key] : null;
    }

    /**
     * 获得流字符串
     *
     * @return string
     */
    public function __toString(): string
    {
        if ($this->isAttached() === false) {
            return '';
        }

        try {
            $this->rewind();
            return $this->getContents();
        } catch (RuntimeException $e) {
            return '';
        }
    }

    /**
     * 判断是否已附加资源
     *
     * @return bool
     */
    protected function isAttached(): bool
    {
        return is_resource($this->stream);
    }

    /**
     * 附加资源
     *
     * @param resource
     *
     * @throws \InvalidArgumentException
     */
    protected function attach($stream)
    {
        if (is_resource($stream) === false) {
            throw new InvalidArgumentException(__METHOD__ . ' argument must be a valid PHP resource');
        }

        $this->stream = $stream;
    }
}
