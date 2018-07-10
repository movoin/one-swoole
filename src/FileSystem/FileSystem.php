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
use One\FileSystem\Contracts\Adapter as AdapterInterface;
use One\FileSystem\Exceptions\FileSystemException;
use One\Support\Helpers\Assert;

class FileSystem
{
    /**
     * 适配器
     *
     * @var array
     */
    protected $adapters = [];

    /**
     * 构造
     *
     * @param array $adapters
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $adapters = [])
    {
        $this->mountAdapters($adapters);
    }

    /**
     * 批量挂载适配器
     *
     * @param  array  $adapters
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function mountAdapters(array $adapters)
    {
        foreach ($adapters as $prefix => $adapter) {
            $this->mountAdapter($prefix, $adapter);
        }

        return $this;
    }

    /**
     * 挂载适配器
     *
     * @param  string                               $prefix
     * @param  \One\FileSystem\Contracts\Adapter    $adapter
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function mountAdapter(string $prefix, AdapterInterface $adapter)
    {
        if (! Assert::stringNotEmpty($prefix)) {
            throw new InvalidArgumentException(sprintf('"%s" parameter 1 must be string', __METHOD__));
        }

        $this->adapters[$prefix] = $adapter;

        return $this;
    }

    /**
     * 获得指定适配器
     *
     * @param  string $prefix
     *
     * @return \One\FileSystem\Contracts\Adapter
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function getAdapter(string $prefix): AdapterInterface
    {
        if (! isset($this->adapters[$prefix])) {
            throw new FileSystemException(sprintf('Not found "%s" adapter', $prefix));
        }

        return $this->adapters[$prefix];
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
    }

    /**
     * 调用适配器中的方法
     *
     * @param  string $method
     * @param  array  $arguments
     *
     * @return mixed
     * @throws \One\FileSystem\Exceptions\FileSystemException
     */
    public function __call($method, $arguments)
    {
        list($prefix, $arguments) = $this->filterPrefix($arguments);

        $adapter = $this->getAdapter($prefix);

        if (method_exists($adapter, $method)) {
            return call_user_func_array([$adapter, $method], $arguments);
        }

        unset($prefix, $arguments, $adapter);

        throw new BadMethodCallException(sprintf(
            '"%s::%s() not exists"',
            __CLASS__,
            __METHOD__
        ));
    }

    /**
     * 从参数中返回适配器前缀
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
            throw new InvalidArgumentException(sprintf('"%s" parameter 1 must be string', __METHOD__));
        }

        list($prefix, $path) = $this->getPrefixAndPath($path);
        array_unshift($arguments, $path);

        return [$prefix, $arguments];
    }

    /**
     * 获得路径中的前缀和路径
     *
     * @param  string $path
     *
     * @return array
     */
    protected function getPrefixAndPath(string $path): array
    {
        if (! Assert::contains($path, '://')) {
            throw new InvalidArgumentException('The adapter prefix is not defined');
        }

        return explode('://', $path, 2);
    }
}
