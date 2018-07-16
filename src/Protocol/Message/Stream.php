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
    private static $readWriteHash = [
        'read' => [
            'r'     => 1, 'w+'  => 1, 'r+'  => 1, 'x+'  => 1, 'c+' => 1,
            'rb'    => 1, 'w+b' => 1, 'r+b' => 1, 'x+b' => 1,
            'c+b'   => 1, 'rt'  => 1, 'w+t' => 1, 'r+t' => 1,
            'x+t'   => 1, 'c+t' => 1, 'a+'  => 1,
        ],
        'write' => [
            'w'     => 1, 'w+'  => 1, 'rw'  => 1, 'r+'  => 1, 'x+' => 1,
            'c+'    => 1, 'wb'  => 1, 'w+b' => 1, 'r+b' => 1,
            'x+b'   => 1, 'c+b' => 1, 'w+t' => 1, 'r+t' => 1,
            'x+t'   => 1, 'c+t' => 1, 'a'   => 1, 'a+'  => 1,
        ],
    ];
    /**
     * 流对象
     *
     * @var resource
     */
    private $stream;
    /**
     * 可定位
     *
     * @var bool
     */
    private $seekable;
    /**
     * 可读取
     *
     * @var bool
     */
    private $readable;
    /**
     * 可写入
     *
     * @var bool
     */
    private $writable;
    /**
     * URI
     *
     * @var mixed
     */
    private $uri;
    /**
     * 数据大小
     *
     * @var int
     */
    private $size;

    /**
     * 构造
     *
     * @param resource|string $content
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($content)
    {
        if (Assert::stringNotEmpty($content)) {
            $resource = fopen('php://temp', 'rw+');
        } elseif (Assert::resource($content)) {
            $resource = $content;
        } else {
            throw new InvalidArgumentException('Parameter 1 must be the `resource` or `string`');
        }

        $meta = stream_get_meta_data($resource);

        $this->stream   = $resource;
        $this->seekable = $meta['seekable'];
        $this->readable = isset(self::$readWriteHash['read'][$meta['mode']]);
        $this->writable = isset(self::$readWriteHash['write'][$meta['mode']]);
        $this->uri      = $this->getMetadata('uri');

        if (Assert::stringNotEmpty($content)) {
            $this->write($content);
        }

        unset($meta, $content, $resource);
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
        if (isset($this->stream)) {
            if (Assert::resource($this->stream)) {
                fclose($this->stream);
            }

            $this->detach();
        }
    }

    /**
     * 分离流中的资源
     *
     * @return resource|null
     */
    public function detach()
    {
        if (! isset($this->stream)) {
            return;
        }

        $resource = $this->stream;

        unset($this->stream);
        $this->size = $this->uri = null;
        $this->readable = $this->writable = $this->seekable = false;

        return $resource;
    }

    /**
     * 获得流的大小
     *
     * @return int|null
     */
    public function getSize()
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if (! isset($this->stream)) {
            return;
        }

        // 清除文件状态缓存
        if ($this->uri) {
            clearstatcache(true, $this->uri);
        }

        $stats = fstat($this->stream);

        if (isset($stats['size'])) {
            return $this->size = $stats['size'];
        }
    }

    /**
     * 获得文件指针读/写的位置
     *
     * @return int
     * @throws \RuntimeException
     */
    public function tell(): int
    {
        $pointer = ftell($this->stream);

        if ($pointer === false) {
            throw new RuntimeException('Unable to get file pointer');
        }

        return $pointer;
    }

    /**
     * 获得数据流是否结束
     *
     * @return bool
     */
    public function eof(): bool
    {
        return ! $this->stream || feof($this->stream);
    }

    /**
     * 判断流是否可定位
     *
     * @return bool
     */
    public function isSeekable(): bool
    {
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
        if (! $this->seekable) {
            throw new RuntimeException('Stream not seekable');
        } elseif (fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException(
                sprintf(
                    'Unable seeks on a file pointer %d whence %s',
                    $offset,
                    var_export($whence, true)
                )
            );
        }
    }

    /**
     * 重新定义至流的开始处
     *
     * @throws \RuntimeException
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * 判断文件流是否可写
     *
     * @return bool
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * 写入内容
     *
     * @param  string $content
     *
     * @return int
     * @throws \RuntimeException
     */
    public function write($content): int
    {
        if (! $this->writable) {
            throw new RuntimeException('Stream not writable');
        }

        $this->size = null;

        $length = fwrite($this->stream, utf8_encode($content));

        if ($length === false) {
            throw new RuntimeException('writing file failed');
        }

        return $length;
    }

    /**
     * 判断文件流是否可读
     *
     * @return bool
     */
    public function isReadable()
    {
        return $this->readable;
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
        if (! $this->readable) {
            throw new RuntimeException('Stream not readable');
        }

        return fread($this->stream, $length);
    }

    /**
     * 获得数据内容
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getContents(): string
    {
        if (! isset($this->stream)) {
            throw new RuntimeException('reading stream failed');
        }

        if (($contents = stream_get_contents($this->stream)) === false) {
            throw new RuntimeException('reading stream failed');
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
        if (! isset($this->stream)) {
            return $key ? null : [];
        } elseif ($key === null) {
            return stream_get_meta_data($this->stream);
        }

        $meta = stream_get_meta_data($this->stream);

        return isset($meta[$key]) ? $meta[$key] : null;
    }

    /**
     * 获得流字符串
     *
     * @return string
     */
    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }

            return $this->getContents();
        } catch (RuntimeException $e) {
            return '';
        }
    }
}
