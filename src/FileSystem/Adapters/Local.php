<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\FileSystem\Adapters
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\FileSystem\Adapters;

use Finfo;
use SplFileInfo;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use One\FileSystem\Adapter;
use One\FileSystem\MimeType;
use One\FileSystem\Exceptions\FileSystemException;
use One\FileSystem\Exceptions\FileExistsException;
use One\FileSystem\Exceptions\FileNotExistsException;
use One\FileSystem\Exceptions\DirectoryExistsException;
use One\FileSystem\Exceptions\DirectoryNotExistsException;
use One\Support\Helpers\Arr;

class Local extends Adapter
{
    /**
     * 文件、目录权限
     *
     * @var array
     */
    protected $permissions = [
        'file' => [
            'public' => 0644,
            'private' => 0600
        ],
        'dir' => [
            'public' => 0755,
            'private' => 0700
        ]
    ];
    /**
     * 文件写入标识
     *
     * @var int
     */
    protected $writeFlags;

    /**
     * 构造
     *
     * @param string $basePath
     * @param int    $writeFlags
     * @param array  $permissions
     *
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function __construct(
        string $basePath,
        int $writeFlags = LOCK_EX,
        array $permissions = []
    ) {
        $this->permissions = array_replace_recursive($this->permissions, $permissions);
        $this->writeFlags = $writeFlags;

        $basePath = is_link($basePath) ? realpath($basePath) : $basePath;

        $this->ensureDirectory($basePath);
        $this->setBasePath($basePath);

        unset($basePath);
    }

    /**
     * 判断文件是否存在
     *
     * @param  string $path
     *
     * @return bool
     */
    public function exists(string $path): bool
    {
        return file_exists($this->applyBasePath($path));
    }

    /**
     * 读取文件内容
     *
     * @param  string $path
     *
     * @return string
     */
    public function read(string $path): string
    {
        $location = $this->applyBasePath($path);
        $content  = file_get_contents($location);

        unset($location);

        return $content;
    }

    /**
     * 从文件中读取数据流
     *
     * @param  string $path
     *
     * @return resource
     */
    public function readStream(string $path)
    {
        $location = $this->applyBasePath($path);
        $stream   = fopen($location, 'rb');

        unset($location);

        return $stream;
    }

    /**
     * 返回目录中的内容
     *
     * @param  string  $directory
     * @param  bool    $recursive
     *
     * @return array
     */
    public function listContents(string $directory = '', bool $recursive = false): array
    {
    }

    /**
     * 写入文件内容
     *
     * @param  string $path
     * @param  string $contents
     * @param  array  $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function write(string $path, string $contents, array $config = []): bool
    {
        $location = $this->applyBasePath($path);
        $this->ensureDirectory(dirname($location));

        if (file_put_contents($location, $contents, $this->writeFlags) === false) {
            return false;
        }

        if (($visibility = Arr::get($config, 'visibility')) !== null) {
            $this->setVisibility($path, $visibility);
        }

        unset($location, $visibility);

        return true;
    }

    /**
     * 以数据流的方式写入文件内容
     *
     * @param  string   $path
     * @param  resource $resource
     * @param  array    $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function writeStream(string $path, $resource, array $config = []): bool
    {
        $location = $this->applyBasePath($path);
        $this->ensureDirectory(dirname($location));
        $stream = fopen($location, 'w+b');

        if (! $stream) {
            return false;
        }

        stream_copy_to_stream($resource, $stream);

        if (! fclose($stream)) {
            return false;
        }

        if (($visibility = Arr::get($config, 'visibility')) !== null) {
            $this->setVisibility($path, $visibility);
        }

        unset($location, $stream, $visibility);

        return true;
    }

    /**
     * 更新文件内容
     *
     * @param  string $path
     * @param  string $contents
     * @param  array  $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function update(string $path, string $contents, array $config = []): bool
    {
        $location = $this->applyBasePath($path);
        $this->ensureDirectory(dirname($location));

        if (file_put_contents($location, $contents, $this->writeFlags) === false) {
            return false;
        }

        if (($visibility = Arr::get($config, 'visibility')) !== null) {
            $this->setVisibility($path, $visibility);
        }

        unset($location, $visibility);

        return true;
    }

    /**
     * 以数据流的方式更新文件内容
     *
     * @param  string   $path
     * @param  resource $resource
     * @param  array    $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function updateStream(string $path, $resource, array $config = []): bool
    {
        $location = $this->applyBasePath($path);
        $this->ensureDirectory(dirname($location));
        $stream = fopen($location, 'w+b');

        if (! $stream) {
            return false;
        }

        stream_copy_to_stream($resource, $stream);

        if (! fclose($stream)) {
            return false;
        }

        if (($visibility = Arr::get($config, 'visibility')) !== null) {
            $this->setVisibility($path, $visibility);
        }

        unset($location, $stream, $visibility);

        return true;
    }

    /**
     * 获得文件的源数据
     *
     * @param  string $path
     *
     * @return array|null
     */
    public function getMetaData(string $path): array
    {
        $location = $this->applyBasePath($path);
        $info     = new SplFileInfo($location);

        unset($location);

        return $this->normalizeFileInfo($info);
    }

    /**
     * 获得文件的类型
     *
     * @param  string $path
     *
     * @return string
     */
    public function getMimeType(string $path): string
    {
        $location = $this->applyBasePath($path);
        $finfo    = new Finfo(FILEINFO_MIME_TYPE);
        $mimetype = $finfo->file($location);

        if (in_array($mimetype, ['application/octet-stream', 'inode/x-empty'])) {
            $mimetype = MimeType::detectByFilePath($location);
        }

        return $mimetype;
    }

    /**
     * 返回文件可见性
     *
     * @param  string $path
     *
     * @return string
     */
    public function getVisibility(string $path): string
    {
        $location = $this->applyBasePath($path);
        clearstatcache(false, $location);
        $permission = octdec(substr(sprintf('%o', fileperms($location)), -4));

        return $permission & 0044 ? self::VIS_PUB : self::VIS_PRI;
    }

    /**
     * 设置文件可见性
     *
     * @param string $path
     * @param string $visibility
     *
     * @return bool
     */
    public function setVisibility(string $path, string $visibility): bool
    {
        $location = $this->applyBasePath($path);
        $type     = is_dir($location) ? 'dir' : 'file';

        return chmod($location, $this->permissions[$type][$visibility]);
    }

    /**
     * 重命名文件
     *
     * @param  string $path
     * @param  string $newpath
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function rename(string $path, string $newpath): bool
    {
        $location       = $this->applyBasePath($path);
        $destination    = $this->applyBasePath($newpath);

        $this->ensureDirectory(dirname($destination));

        return rename($location, $destination);
    }

    /**
     * 删除文件
     *
     * @param  string $path
     *
     * @return bool
     */
    public function delete(string $path): bool
    {
        return unlink($this->applyBasePath($path));
    }

    /**
     * 创建目录
     *
     * @param  string $dirname
     * @param  array  $config
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\DirectoryExistsException
     */
    public function createDir(string $dirname, array $config = []): bool
    {
        $location = $this->applyBasePath($dirname);

        if (is_dir($location)) {
            throw new DirectoryExistsException($dirname);
        }

        $umask = umask(0);
        $visibility = Arr::get($config, 'visibility', 'public');

        if (mkdir($location, $this->permissions['dir'][$visibility], true)) {
            umask($umask);

            return true;
        }

        return false;
    }

    /**
     * 删除目录
     *
     * @param  string $dirname
     *
     * @return bool
     * @throws \One\FileSystem\Exceptions\DirectoryNotExistsException
     */
    public function deleteDir(string $dirname): bool
    {
        $location = $this->applyBasePath($dirname);

        if (! is_dir($location)) {
            throw new DirectoryNotExistsException($dirname);
        }

        $contents = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($location, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($contents as $file) {
            if ($file->isReadable()) {
                switch ($file->getType()) {
                    case 'dir':
                        rmdir($file->getRealPath());
                        break;
                    case 'link':
                        unlink($file->getPathname());
                        break;
                    default:
                        unlink($file->getRealPath());
                        break;
                }
            }
        }

        unset($contents);

        return rmdir($location);
    }

    /**
     * 确认目录存在
     *
     * @param  string $path
     *
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    protected function ensureDirectory(string $path)
    {
        if (! is_dir($path)) {
            $umask = umask(0);
            @mkdir($path, $this->permissions['dir']['public'], true);
            umask($umask);

            if (! is_dir($path)) {
                throw new FileSystemException(sprintf('Unable to create "%s" directory', $path));
            }
        }

        if (! is_readable($path)) {
            throw new FileSystemException(sprintf('Unable to access "%s" directory', $path));
        }
    }

    /**
     * 标准化文件信息
     *
     * @param  \SplFileInfo $file
     *
     * @return array|null
     */
    protected function normalizeFileInfo(SplFileInfo $file)
    {
        if (! $file->isLink()) {
            $normalized = [
                'type'      => $file->getType(),
                'path'      => $this->getFilePath($file),
                'timestamp' => $file->getMTime()
            ];

            if ($normalized['type'] === 'file') {
                $normalized['size'] = $file->getSize();
            }

            return $normalized;
        }
    }

    /**
     * 从 SplFileInfo 中返回完整文件路径
     *
     * @param  \SplFileInfo $file
     *
     * @return string
     */
    protected function getFilePath(SplFileInfo $file): string
    {
        $location = $file->getPathName();
        $path     = $this->removeBasePath($location);

        unset($location);

        return trim(str_replace('\\', '/', $path), '/');
    }
}
