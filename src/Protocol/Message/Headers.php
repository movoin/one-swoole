<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Message
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Message;

use One\Protocol\Contracts\Headers as HeadersInterface;
use One\Support\Collection;

class Headers extends Collection implements HeadersInterface
{
    /**
     * 判断是否存在指定键名数据
     *
     * @param  string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return parent::has($this->normalizeKey($key));
    }

    /**
     * 获得所有数据
     *
     * @return array
     */
    public function all(): array
    {
        $all = parent::all();
        $out = [];

        foreach ($all as $key => $props) {
            $out[$props['originalKey']] = $props['value'];
        }

        unset($all);
        return $out;
    }

    /**
     * 返回指定数据项
     *
     * @param  string   $key
     * @param  mixed    $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($this->has($key)) {
            return parent::get($this->normalizeKey($key))['value'];
        }

        return $default;
    }

    /**
     * 获得原键名
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return string
     */
    public function getOriginalKey(string $key, $default = null)
    {
        if ($this->has($key)) {
            return parent::get($this->normalizeKey($key))['originalKey'];
        }

        return $default;
    }

    /**
     * 设置数据项
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value)
    {
        if (! is_array($value)) {
            $value = [$value];
        }

        parent::set($this->normalizeKey($key), [
            'value' => $value,
            'originalKey' => $key
        ]);
    }

    /**
     * 添加头信息
     *
     * @param string $key
     * @param mixed  $value
     */
    public function add(string $key, $value)
    {
        $oldValues = $this->get($key, []);
        $newValues = is_array($value) ? $value : [$value];
        $this->set($key, array_merge($oldValues, array_values($newValues)));

        unset($oldValues, $newValues);
    }

    /**
     * 删除指定数据项
     *
     * @param  string $key
     */
    public function remove(string $key)
    {
        parent::remove($this->normalizeKey($key));
    }

    /**
     * 标准化键名
     *
     * @param  string $key
     *
     * @return string
     */
    public function normalizeKey(string $key): string
    {
        $key = strtr(strtolower(trim($key)), '_', '-');

        if (strpos($key, 'http-') === 0) {
            $key = substr($key, 5);
        }

        return $key;
    }
}
