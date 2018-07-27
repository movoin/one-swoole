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
     * 忽略类型
     *
     * @var array
     */
    protected $skips = [
        'abstract'  => 0,
        'interface' => 0,
        'trait'     => 0,
    ];

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
     * 设置忽略类型
     *
     * @param  string $name
     *
     * @return self
     */
    public function withSkip(string $name): self
    {
        $clone = clone $this;

        if (array_key_exists($name, $clone->skips)) {
            $clone->skips[$name] = 1;
        }

        return $clone;
    }

    /**
     * 设置全部忽略
     *
     * @return self
     */
    public function withSkipAll(): self
    {
        $clone = clone $this;

        foreach ($clone->skips as $name => $value) {
            $clone->skips[$name] = 1;
        }

        return $clone;
    }

    /**
     * 设置全部不忽略
     *
     * @return self
     */
    public function withNotSkipAll(): self
    {
        $clone = clone $this;

        foreach ($clone->skips as $name => $value) {
            $clone->skips[$name] = 0;
        }

        return $clone;
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
     * 获得路径对应的命名空间
     *
     * @param  \SplFileInfo $file
     *
     * @return string
     */
    public function getClassName(SplFileInfo $file): string
    {
        return
            rtrim($this->getRootNamespace(), '\\') .
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
    public function getRootNamespace(): string
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
        return $file->isFile() &&
                $this->hasExtension($file) &&
                $this->instanceOf($file)
        ;
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
     * 判断文件是否实现于指定接口
     *
     * @param  \SplFileInfo $file
     *
     * @return bool
     */
    protected function instanceOf(SplFileInfo $file): bool
    {
        $reflecter = new ReflectionClass($this->getClassName($file));

        if (! $this->isSkip($reflecter)) {
            if ($this->interface === null) {
                return true;
            }
            return (bool) $reflecter->implementsInterface($this->interface);
        }

        unset($reflecter);

        return false;
    }

    /**
     * 判断是否忽略
     *
     * @param  \ReflectionClass $reflecter
     *
     * @return bool
     */
    protected function isSkip(ReflectionClass $reflecter): bool
    {
        $skips = array_filter(
            $this->skips,
            function ($value) {
                return $value === 1;
            }
        );

        foreach ($skips as $name => $value) {
            $method = 'is' . ucfirst($name);

            if ($reflecter->$method()) {
                return true;
            }

            unset($method);
        }

        unset($skips);

        return false;
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
