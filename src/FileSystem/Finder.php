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

use CallbackFilterIterator;
use FilesystemIterator;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use One\Support\Helpers\Arr;
use One\Support\Helpers\Json;

class Finder implements IteratorAggregate
{
    /**
     * 遍历根路径
     *
     * @var string
     */
    protected $path;
    /**
     * 匹配接口
     *
     * @var string
     */
    protected $interface;
    /**
     * 匹配扩展名
     *
     * @var string
     */
    protected $extension;

    /**
     * 根路径
     *
     * @var string
     */
    private $rootPath;
    /**
     * 应用路径，用于对应命名空间
     *
     * @var string
     */
    private $appPath;
    /**
     * 命名空间
     *
     * @var string
     */
    private static $namespace;

    /**
     * 构造
     *
     * @param string $path
     * @param string $interface
     * @param string $extension
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $path, string $interface = null, string $extension = null)
    {
        $this->assertPresent($path);

        $this->path = $path;
        $this->interface = $interface;
        $this->extension = $extension;
    }

    /**
     * 获得路径对应的命名空间
     *
     * @param  \SplFileInfo $file
     *
     * @return string
     */
    public static function getClassName(SplFileInfo $file): string
    {
        return
            rtrim(static::getRootNamespace(), '\\') .
            str_replace('/', '\\', str_replace($this->getAppPath(), '', $file->getPath())) .
            '\\' .
            $file->getBasename('.' . $file->getExtension())
        ;
    }

    /**
     * 获得应用命名空间
     *
     * @return string
     */
    public static function getRootNamespace(): string
    {
        if (! is_null(static::$namespace)) {
            return static::$namespace;
        }

        $composer = Json::decode(file_get_contents($this->getRootPath() . '/composer.json'));

        foreach ((array) Arr::get($composer, 'autoload.psr-4', []) as $namespace => $paths) {
            foreach ((array) $paths as $path) {
                if (realpath($this->getAppPath()) === realpath($this->getRootPath() . '/' . $path)) {
                    return static::$namespace = $namespace;
                }
            }
        }

        unset($composer);

        return '';
    }

    /**
     * 获得根目录路径
     *
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath ?? ROOT_PATH;
    }

    /**
     * 设置根目录路径
     *
     * @param string $path
     */
    public function setRootPath(string $path)
    {
        $this->assertPresent($path);

        $this->rootPath = $path;
    }

    /**
     * 获得应用目录路径
     *
     * @return string
     */
    public function getAppPath()
    {
        return $this->appPath ?? APP_PATH;
    }

    /**
     * 设置应用目录路径
     *
     * @param string $path
     */
    public function setAppPath(string $path)
    {
        $this->assertPresent($path);

        $this->appPath = $path;
    }

    /**
     * 设置路径
     *
     * @param string $path
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withPath(string $path): self
    {
        $this->assertPresent($path);

        if ($this->path === $path) {
            return $this;
        }

        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    /**
     * 设置匹配接口
     *
     * @param string $interface
     *
     * @return self
     */
    public function withInterface(string $interface): self
    {
        if ($this->interface === $interface) {
            return $this;
        }

        $clone = clone $this;
        $clone->interface = $interface;

        return $clone;
    }

    /**
     * 设置文件扩展名
     *
     * @param  string $extension
     *
     * @return self
     */
    public function withExtension(string $extension): self
    {
        if ($this->extension === $extension) {
            return $this;
        }

        $clone = clone $this;
        $clone->extension = strtolower($extension);

        return $clone;
    }

    /**
     * 获得扁平化结果
     *
     * @return /Iterator
     */
    public function getIterator(): Iterator
    {
        return new CallbackFilterIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            ),
            function ($current) {
                return $this->match($current);
            }
        );
    }

    /**
     * 匹配文件
     *
     * @param  \SplFileInfo $file
     *
     * @return bool
     */
    protected function match(SplFileInfo $file): bool
    {
        return $file->isFile() && $this->hasExtension($file) && $this->instanceOf($file);
    }

    /**
     * 判断文件是否实现于指定接口
     *
     * @param  \SplFileInfo $file
     *
     * @return bool
     */
    protected function instanceOf(SplFileInfo $file): bool
    {
        if ($this->interface === null) {
            return true;
        }

        $reflecter = new ReflectionClass(static::getClassName($file));

        return (bool) $reflecter->implementsInterface($this->interface);
    }

    /**
     * 判断文件是否为指定扩展名
     *
     * @param  \SplFileInfo $file
     *
     * @return bool
     */
    protected function hasExtension(SplFileInfo $file): bool
    {
        if ($this->extension === null) {
            return true;
        }

        return strtolower($file->getExtension()) === strtolower($this->extension);
    }

    /**
     * 确保目录存在
     *
     * @param  string $path
     */
    private function assertPresent(string $path)
    {
        if (! is_dir($path)) {
            throw new InvalidArgumentException('Directory `' . $path . '` does not exists');
        }
    }
}
