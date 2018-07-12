<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\FileSystem
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\FileSystem;

use InvalidArgumentException;
use One\FileSystem\ContentListingFormatter;
use One\FileSystem\Contracts\Adapter;
use One\FileSystem\Exceptions\FileSystemException;
use One\FileSystem\Exceptions\FileExistsException;
use One\FileSystem\Exceptions\FileNotExistsException;
use One\FileSystem\Exceptions\DirectoryExistsException;
use One\FileSystem\Exceptions\DirectoryNotExistsException;
use One\Support\Helpers\Assert;

class FileSystem
{
    /**
     * 文件系统适配器
     *
     * @var \One\FileSystem\Contracts\Adapter
     */
    protected $adapter;
    /**
     * 配置信息
     *
     * @var array
     */
    protected $config = [];

    /**
     * 构造
     *
     * @param \One\FileSystem\Contracts\Adapter $adapter
     * @param array                             $config
     */
    public function __construct(Adapter $adapter, array $config = [])
    {
        $this->adapter = $adapter;
        $this->config = $config;
    }

    /**
     * 获得文件系统适配器
     *
     * @return \One\FileSystem\Contracts\Adapter
     */
    public function getAdapter(): Adapter
    {
        return $this->adapter;
    }

    /**
     * 判断路径是否存在
     *
     * @param  string $path
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function exists(string $path): bool
    {
        $path = $this->normalizePath($path);

        return strlen($path) === 0 ? false : $this->getAdapter()->exists($path);
    }

    /**
     * 读取文件内容
     *
     * @param  string $path
     *
     * @return string
     * @throws \One\FileSystem\Exceptions\FileSystemException
     * @throws \One\FileSystem\Exceptions\FileNotExistsException
     */
    public function read(string $path): string
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->read($path);
    }

    /**
     * 读取文件流
     *
     * @param  string $path
     *
     * @return resource
     * @throws \One\FileSystem\Exceptions\FileSystemException
     * @throws \One\FileSystem\Exceptions\FileNotExistsException
     */
    public function readStream(string $path)
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->readStream($path);
    }

    /**
     * 读取文件内容并删除文件
     *
     * @param  string $path
     *
     * @return string
     * @throws \One\FileSystem\Exceptions\FileSystemException
     * @throws \One\FileSystem\Exceptions\FileNotExistsException
     */
    public function readAndDelete(string $path): string
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);
        $contents = $this->read($path);
        $this->delete($path);

        return $contents;
    }

    /**
     * 返回目录内容
     *
     * @param  string $directory
     * @param  bool   $recursive
     *
     * @return array
     */
    public function listContents(string $directory = '', bool $recursive = false): array
    {
        $directory = $this->normalizePath($directory);
        $list = $this->getAdapter()->listContents($directory, $recursive);

        return (new ContentListingFormatter($directory, $recursive))->formatListing($list);
    }

    /**
     * 写入新文件
     *
     * @param  string $path
     * @param  string $contents
     * @param  array  $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     * @throws \One\FileSystem\Exceptions\FileExistsException
     */
    public function write(string $path, string $contents, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $this->assertAbsent($path);
        $config = $this->prepareConfig($config);

        return $this->getAdapter()->write($path, $contents, $config);
    }

    /**
     * 从 Stream 写入新文件
     *
     * @param  string   $path
     * @param  resource $resource
     * @param  array    $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     * @throws \One\FileSystem\Exceptions\FileExistsException
     */
    public function writeStream(string $path, $resource, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $this->assertAbsent($path);
        $config = $this->prepareConfig($config);
        $this->rewindStream($resource);

        return $this->getAdapter()->writeStream($path, $resource, $config);
    }

    /**
     * 更新文件
     *
     * @param  string $path
     * @param  string $contents
     * @param  array  $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     * @throws \One\FileSystem\Exceptions\FileNotExistsException
     */
    public function update(string $path, string $contents, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);
        $config = $this->prepareConfig($config);

        return $this->getAdapter()->update($path, $contents, $config);
    }

    /**
     * 从 Stream 更新文件
     *
     * @param  string   $path
     * @param  resource $resource
     * @param  array    $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     * @throws \One\FileSystem\Exceptions\FileNotExistsException
     */
    public function updateStream(string $path, $resource, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);
        $config = $this->prepareConfig($config);
        $this->rewindStream($resource);

        return $this->getAdapter()->updateStream($path, $resource, $config);
    }

    /**
     * 创建或更新文件
     *
     * @param  string $path
     * @param  string $contents
     * @param  array  $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function put(string $path, string $contents, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $config = $this->prepareConfig($config);

        if (! $this->exists($path)) {
            return $this->getAdapter()->update($path, $contents, $config);
        }

        return $this->getAdapter()->write($path, $contents, $config);
    }

    /**
     * 从 Stream 创建或更新文件
     *
     * @param  string   $path
     * @param  resource $resource
     * @param  array    $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function putStream(string $path, $resource, array $config = []): bool
    {
        $path = $this->normalizePath($path);
        $config = $this->prepareConfig($config);
        $this->rewindStream($resource);

        if (! $this->exists($path)) {
            return $this->getAdapter()->updateStream($path, $resource, $config);
        }

        return $this->getAdapter()->writeStream($path, $resource, $config);
    }

    /**
     * 重命名文件
     *
     * @param  string $path
     * @param  string $newpath
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     * @throws \One\FileSystem\Exceptions\FileExistsException
     * @throws \One\FileSystem\Exceptions\FileNotExistsException
     */
    public function rename(string $path, string $newpath): bool
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        $newpath = $this->normalizePath($newpath);
        $this->assertAbsent($newpath);

        return $this->getAdapter()->rename($path, $newpath);
    }

    /**
     * 删除文件
     *
     * @param  string $path
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     * @throws \One\FileSystem\Exceptions\FileNotExistsException
     */
    public function delete(string $path): bool
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->delete($path);
    }

    /**
     * 创建目录
     *
     * @param  string $dirname
     * @param  array  $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     * @throws \One\FileSystem\Exceptions\DirectoryExistsException
     */
    public function createDir(string $dirname, array $config = []): bool
    {
        $dirname = $this->normalizePath($dirname);
        $config = $this->prepareConfig($config);

        return $this->getAdapter()->createDir($dirname, $config);
    }

    /**
     * 删除目录
     *
     * @param  string $dirname
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     * @throws \One\FileSystem\Exceptions\DirectoryNotExistsException
     */
    public function deleteDir(string $dirname): bool
    {
        $dirname = $this->normalizePath($dirname);

        if ($dirname === '') {
            throw new DirectoryNotExistsException($dirname);
        }

        return $this->getAdapter()->deleteDir($dirname);
    }

    /**
     * 获得文件类型信息
     *
     * @param  string $path
     *
     * @return string
     */
    public function getMimeType(string $path): string
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->getMimeType($path);
    }

    /**
     * 获得路径源信息
     *
     * @param  string $path
     *
     * @return array
     */
    public function getMetaData(string $path): array
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->getMetaData($path);
    }

    /**
     * 获得路径可见性
     *
     * @param  string $path
     *
     * @return string
     */
    public function getVisibility(string $path): string
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->getVisibility($path);
    }

    /**
     * 设置路径可见性
     *
     * @param  string $path
     * @param  string $visibility
     *
     * @return bool
     */
    public function setVisibility(string $path, string $visibility): bool
    {
        $path = $this->normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->setVisibility($path, $visibility);
    }

    /**
     * 断言文件存在
     *
     * @param  string $path
     *
     * @throws \One\FileSystem\Exceptions\FileNotExistsException
     */
    protected function assertPresent(string $path)
    {
        if (! $this->exists($path)) {
            throw new FileNotExistsException($path);
        }
    }

    /**
     * 断言文件不存在
     *
     * @param  string $path
     *
     * @throws \One\FileSystem\Exceptions\FileExistsException
     */
    protected function assertAbsent(string $path)
    {
        if ($this->exists($path)) {
            throw new FileExistsException($path);
        }
    }

    /**
     * 预处理配置信息
     *
     * @param  array  $config
     *
     * @return array
     */
    protected function prepareConfig(array $config): array
    {
        return array_merge_recursive($this->config, $config);
    }

    /**
     * 标准化路径
     *
     * @param  string $path
     *
     * @return string
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    protected function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = $this->removePathWhiteSpace($path);

        $parts = [];

        foreach (explode('/', $path) as $part) {
            switch ($part) {
                case '':
                case '.':
                    break;

                case '..':
                    if (empty($parts)) {
                        throw new FileSystemException(sprintf(
                            'The path is outside the root directory: "%s"',
                            $path
                        ));
                    }
                    array_pop($parts);
                    break;

                default:
                    $parts[] = $part;
                    break;
            }
        }

        return implode('/', $parts);
    }

    /**
     * 删除路径空格
     *
     * @param  string $path
     *
     * @return string
     */
    protected function removePathWhiteSpace(string $path): string
    {
        while (preg_match('#\p{C}+|^\./#u', $path)) {
            $path = preg_replace('#\p{C}+|^\./#u', '', $path);
        }

        return $path;
    }

    /**
     * 倒回文件指针的位置
     *
     * @param resource $resource
     *
     * @throws \InvalidArgumentException
     */
    protected function rewindStream($resource)
    {
        if (! Assert::resource($resource)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s::%s() parameter 1 must be "resource"',
                    __CLASS__,
                    __METHOD__
                )
            );
        }

        if (ftell($resource) !== 0 && $this->isSeekableStream($resource)) {
            rewind($resource);
        }
    }

    /**
     * 返回是否可以在当前流中定位
     *
     * @param  resource $resource
     *
     * @return bool
     */
    protected function isSeekableStream($resource): bool
    {
        if (! Assert::resource($resource)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s::%s() parameter 1 must be "resource"',
                    __CLASS__,
                    __METHOD__
                )
            );
        }

        $metadata = stream_get_meta_data($resource);

        return $metadata['seekable'];
    }
}
