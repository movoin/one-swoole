<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Support
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Support;

use Closure;
use ReflectionClass;
use ReflectionException;
use Psr\Container\ContainerInterface;
use One\Support\Exceptions\ContainerException;
use One\Support\Exceptions\ContainerValueNotFoundException;

class Container implements ContainerInterface
{
    /**
     * 别名
     *
     * @var array
     */
    private $alias = [];
    /**
     * 绑定
     *
     * @var array
     */
    private $bindings = [];
    /**
     * 对象实例
     *
     * @var array
     */
    private $objects = [];

    /**
     * 判断是否存在对象
     *
     * @param  string $id
     *
     * @return bool
     */
    public function has($id): bool
    {
        return isset($this->alias[$id]) ||
               isset($this->bindings[$id]) ||
               isset($this->objects[$id]);
    }

    /**
     * 获得实例
     *
     * @param  string $id
     *
     * @return object
     * @throws \One\Support\Exceptions\ContainerException
     * @throws \One\Support\Exceptions\ContainerValueNotFoundException
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->make($id);
        }

        throw new ContainerValueNotFoundException(sprintf(
            'Identifier "%s" is not defined.',
            $id
        ));
    }

    /**
     * 映射别名
     *
     * @param  string $id
     * @param  string $alias
     */
    public function alias($id, $alias)
    {
        $this->alias[$alias] = $id;
    }

    /**
     * 绑定对象申明过程，对象将在调用时实例化
     *
     * @param  string $id
     * @param  mixed  $concrete
     *
     * @throws \One\Support\Exceptions\ContainerException
     */
    public function bind($id, $concrete = null)
    {
        if ($concrete === null) {
            $concrete = $id;
        }

        if (! $concrete instanceof Closure) {
            $concrete = function ($container, $parameters = []) use ($id, $concrete) {
                if ($id === $concrete) {
                    return $container->resolve($concrete, $parameters);
                }
                return $container->make($concrete, $parameters);
            }
        }

        $this->bindings[$id] = $concrete;
    }

    /**
     * 获得对象实例
     *
     * @param  string $id
     * @param  array  $parameters
     * @param  bool   $createNew
     *
     * @return object
     * @throws \One\Support\Exceptions\ContainerException
     */
    public function make($id, array $parameters = [], $createNew = false)
    {
        if (isset($this->alias[$id])) {
            $id = $this->alias[$id];
        }

        if ($createNew || ! isset($this->objects[$id])) {
            $concrete = isset($this->bindings[$id]) ?
                        $this->bindings[$id] :
                        $id;

            $this->objects[$id] = $this->resolve($concrete, $parameters);
        }

        return $this->objects[$id];
    }

    /**
     * 实例化对象
     *
     * @param  mixed  $concrete
     * @param  array  $parameters
     *
     * @return object
     * @throws \One\Support\Exceptions\ContainerException
     */
    public function resolve($concrete, array $parameters = [])
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new ContainerException(
                sprintf('Container error while retrieving "%s"', $concrete),
                null,
                $e
            );
        }

        return $reflector->newInstanceArgs($parameters);
    }
}
