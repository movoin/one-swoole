<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Support\Contracts
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Support\Contracts;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use One\Support\Contracts\Arrayable;

interface Collectable extends ArrayAccess, Countable, IteratorAggregate, Arrayable
{
    /**
     * 设置数据项
     *
     * @param string    $key
     * @param mixed     $value
     */
    public function set(string $key, $value);

    /**
     * 返回指定数据项
     *
     * @param  string   $key
     * @param  mixed    $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * 替换所有数据
     *
     * @param  array    $items
     */
    public function replace(array $items = []);

    /**
     * 获得所有数据
     *
     * @return array
     */
    public function all(): array;

    /**
     * 判断是否存在指定键名数据
     *
     * @param  string   $key
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * 删除指定数据项
     *
     * @param  string   $key
     */
    public function remove(string $key);

    /**
     * 清空集合
     */
    public function clear();
}
