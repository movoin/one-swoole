<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Event
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Event;

use One\Support\Contracts\Arrayable;

/**
 * 事件上下文对象
 *
 * @package     One\Event
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */
class Context implements Arrayable
{
    /**
     * 上下文内容项
     *
     * @var array
     */
    private $items = [];

    /**
     * 判断是否存在指定上下文
     *
     * @param  string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->items[$name]);
    }

    /**
     * 设置上下文
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set(string $name, $value)
    {
        $this->items[$name] = $value;
    }

    /**
     * 获得指定上下文
     *
     * @param  string $name
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        if ($this->has($name)) {
            return $this->items[$name];
        }

        return $default;
    }

    /**
     * 获得数组
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
