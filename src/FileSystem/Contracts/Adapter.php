<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\FileSystem\Contracts
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\FileSystem\Contracts;

interface Adapter
{
    /**
     * 公有
     */
    const VIS_PUB = 'public';
    /**
     * 私有
     */
    const VIS_PRI = 'private';

    /**
     * 判断文件是否存在
     *
     * @param  string $path
     *
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * 读取文件内容
     *
     * @param  string $path
     *
     * @return string
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     */
    public function read(string $path): string;

    /**
     * 从文件中读取数据流
     *
     * @param  string $path
     *
     * @return resource
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     */
    public function readStream(string $path): resource;

    /**
     * 获得文件的源数据
     *
     * @param  string $path
     *
     * @return array
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     */
    public function getMetaData(string $path): array;

    /**
     * 获得文件大小
     *
     * @param  string $path
     *
     * @return int
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     */
    public function getSize(string $path): int;

    /**
     * 获得文件的时间戳
     *
     * @param  string $path
     *
     * @return int
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     */
    public function getTimestamp(string $path): int;

    /**
     * 获得文件的类型
     *
     * @param  string $path
     *
     * @return string
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     */
    public function getMimeType(string $path): string;

    /**
     * 返回文件可见性
     *
     * @param  string $path
     *
     * @return string
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     */
    public function getVisibility(string $path): string;

    /**
     * 设置文件可见性
     *
     * @param string $path
     * @param string $visibility
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     */
    public function setVisibility(string $path, string $visibility): bool;

    /**
     * 写入文件内容
     *
     * @param  string $path
     * @param  string $contents
     * @param  array  $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\PathExistsException
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function write(string $path, string $contents, array $config = []): bool;

    /**
     * 以数据流的方式写入文件内容
     *
     * @param  string   $path
     * @param  resource $resource
     * @param  array    $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\PathExistsException
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function writeStream(string $path, resource $resource, array $config = []): bool;

    /**
     * 更新文件内容
     *
     * @param  string $path
     * @param  string $contents
     * @param  array  $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function update(string $path, string $contents, array $config = []): bool;

    /**
     * 以数据流的方式更新文件内容
     *
     * @param  string   $path
     * @param  resource $resource
     * @param  array    $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function updateStream(string $path, resource $resource, array $config = []): bool;

    /**
     * 重命名文件
     *
     * @param  string $path
     * @param  string $newpath
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     * @throws \One\FileSystem\Exceptions\PathExistsException
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function rename(string $path, string $newpath): bool;

    /**
     * 复制文件
     *
     * @param  string $path
     * @param  string $newpath
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     * @throws \One\FileSystem\Exceptions\PathExistsException
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function copy(string $path, string $newpath): bool;

    /**
     * 删除文件
     *
     * @param  string $path
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     */
    public function delete(string $path): bool;

    /**
     * 创建目录
     *
     * @param  string $dirname
     * @param  array  $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\PathExistsException
     */
    public function createDir(string $dirname, array $config = []): bool;

    /**
     * 删除目录
     *
     * @param  string $dirname
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\PathNotExistsException
     */
    public function deleteDir(string $dirname): bool;
}
