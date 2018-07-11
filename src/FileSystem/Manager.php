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

use BadMethodCallException;
use InvalidArgumentException;
use One\Support\Helpers\Assert;

class Manager
{
    /**
     * 已挂载文件系统
     *
     * @var array
     */
    protected $fs = [];

    /**
     * 构造
     *
     * @param array $fileSystems
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $fileSystems = [])
    {
        $this->mountFileSystems($fileSystems);
    }

    /**
     * 获得指定文件系统实例
     *
     * @param  string $prefix
     *
     * @return \One\FileSystem\FileSystem
     * @throws \InvalidArgumentException
     */
    public function getFileSystem(string $prefix): FileSystem
    {
        if (! isset($this->fs[$prefix])) {
            throw new InvalidArgumentException(
                sprintf('The filesystem was not found: "%s"', $prefix)
            );
        }

        return $this->fs[$prefix];
    }

    /**
     * 批量挂载文件系统
     *
     * @param  array  $fileSystems
     *
     * @return self
     */
    public function mountFileSystems(array $fileSystems = []): self
    {
        foreach ($fileSystems as $prefix => $fileSystem) {
            $this->mountFileSystem($prefix, $fileSystem);
        }

        return $this;
    }

    /**
     * 挂载文件系统
     *
     * @param  string                       $prefix
     * @param  One\FileSystem\FileSystem    $fileSystem
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function mountFileSystem(string $prefix, FileSystem $fileSystem): self
    {
        if (! Assert::stringNotEmpty($prefix)) {
            throw new InvalidArgumentException(
                sprintf('"%s" parameter 1 must be string', __METHOD__)
            );
        }

        $this->fs[$prefix] = $fileSystem;

        return $this;
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
    }

    /**
     * 复制文件
     *
     * @param  string $from
     * @param  string $to
     * @param  array  $config
     *
     * @return bool
     */
    public function copy(string $from, string $to, array $config = []): bool
    {
        list($prefixFrom, $from) = $this->getPrefixAndPath($from);

        if (($buffer = $this->getFileSystem($prefixFrom)->readStream($from)) === false) {
            return false;
        }

        unset($prefixFrom, $from);

        list($prefixTo, $to) = $this->getPrefixAndPath($to);

        if ($this->getFileSystem($prefixTo)->writeStream($to, $buffer, $config)) {
            unset($prefixTo, $to, $buffer);
            return true;
        }

        return false;
    }

    /**
     * 移动文件
     *
     * @param  string $from
     * @param  string $to
     * @param  array  $config
     *
     * @return bool
     */
    public function move(string $from, string $to, array $config = []): bool
    {
        list($prefixFrom, $pathFrom) = $this->getPrefixAndPath($from);
        list($prefixTo, $pathTo) = $this->getPrefixAndPath($to);

        if ($prefixFrom === $prefixTo) {
            $fs = $this->getFileSystem($prefixFrom);
            $renamed = $fs->rename($pathFrom, $pathTo);

            if ($renamed && isset($config['visibility'])) {
                return $fs->setVisibility($pathTo, $config['visibility']);
            }

            unset($fs, $prefixFrom, $pathFrom, $prefixTo, $pathTo);

            return $renamed;
        }

        if ($this->copy($from, $to, $config)) {
            return $this->delete($from);
        }

        return false;
    }

    /**
     * 调用适配器中的方法
     *
     * @param  string $method
     * @param  array  $arguments
     *
     * @return mixed
     * @throws \BadMethodCallException
     * @throws \InvalidArgumentException
     */
    public function __call($method, $arguments)
    {
        list($prefix, $arguments) = $this->filterPrefix($arguments);

        $fs = $this->getFileSystem($prefix);

        if (method_exists($fs, $method)) {
            return call_user_func_array([$fs, $method], $arguments);
        }

        $class = get_class($fs);

        unset($prefix, $arguments, $fs);

        throw new BadMethodCallException(sprintf(
            '"%s::%s() not exists"',
            $class,
            __METHOD__
        ));
    }

    /**
     * 从参数中获得文件系统前缀
     *
     * @param  array  $arguments
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function filterPrefix(array $arguments)
    {
        if (empty($arguments)) {
            throw new InvalidArgumentException('The arguments cannot be empty');
        }

        $path = array_shift($arguments);

        if (! Assert::stringNotEmpty($path)) {
            throw new InvalidArgumentException(
                sprintf('"%s" path must be string', __METHOD__)
            );
        }

        list($prefix, $path) = $this->getPrefixAndPath($path);
        array_unshift($arguments, $path);
        unset($path);

        return [$prefix, $arguments];
    }

    /**
     * 获得路径中的前缀和路径
     *
     * @param  string $path
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getPrefixAndPath(string $path): array
    {
        if (! Assert::contains($path, '://')) {
            throw new InvalidArgumentException('The filesystem prefix does not defined');
        }

        return explode('://', $path, 2);
    }
}
