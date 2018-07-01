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

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use One\Support\Contracts\Arrayable;
use One\Support\Contracts\Jsonable;
use One\Support\Helpers\Json;

class Collection implements ArrayAccess, Countable, IteratorAggregate, Arrayable, Jsonable
{
    /**
     * 数据集合
     *
     * @var array
     */
    protected $items = [];

    /**
     * 构造
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->replace($items);
    }

    /**
     * 获得所有数据
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * 获得所有数据键名
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->items);
    }

    /**
     * 替换所有数据
     *
     * @param  array  $items
     */
    public function replace(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * 更新数据
     *
     * @param  array  $items
     */
    public function update(array $items)
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * 遍历数据
     *
     * @param  callable $callback
     *
     * @return array
     */
    public function map(callable $callback): array
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);

        return array_combine($keys, $items);
    }

    /**
     * 返回过滤数据
     *
     * @param  callable $callback
     *
     * @return array
     */
    public function filter(callable $callback): array
    {
        return array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * 返回数据集合大小
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * 清空集合
     */
    public function clear()
    {
        foreach ($this->items as &$item) {
            unset($item);
        }

        $this->items = [];
    }

    /**
     * 判断是否存在指定键名数据
     *
     * @param  string $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * 返回指定数据项
     *
     * @param  mixed $key
     * @param  mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return ($value = $this->offsetGet($key)) === null ? $default : $value;
    }

    /**
     * 返回与指定键名的值相符的数据
     *
     * @param  mixed $key
     * @param  mixed $value
     *
     * @return mixed
     */
    public function getByValue($key, $value)
    {
        return array_filter($this->items, function ($val) use ($key, $value) {
            return $val[$key] === $value;
        });
    }

    /**
     * 设置数据项
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * 推入数据项
     *
     * @param  mixed $item
     */
    public function push($item)
    {
        $this->offsetSet(null, $item);
    }

    /**
     * 推入多条数据
     *
     * @param  array $items
     */
    public function pushMany($items = [])
    {
        foreach ($items as $item) {
            $this->push($item);
        }
    }

    /**
     * 返回并删除第一条数据项
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * 返回并删除最后一条数据项
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * 设置数据项
     *
     * @param  mixed $offset
     * @param  mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * 返回是否存在数据项
     *
     * @param  mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * 删除数据项
     *
     * @param  mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * 返回数据项
     *
     * @param  mixed $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * 返回迭代器
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * 返回数组数据
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * 获得 JSON 字符串
     *
     * @return string
     */
    public function toJson(): string
    {
        return Json::encode($this->items);
    }
}
