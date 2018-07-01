<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Support\Helpers
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Support\Helpers;

use stdClass;
use InvalidArgumentException;
use One\Support\Helpers\Assert;
use One\Support\Helpers\Json;

final class Arr
{
    /**
     * 返回数组是否存在指定键名
     *
     * @param  \ArrayAccess|array   $array
     * @param  string|int           $key
     *
     * @return bool
     */
    public static function exists($array, $key): bool
    {
        if (Assert::instanceOf($array, 'ArrayAccess')) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * 从数组中获得指定键名的值，并支持使用 `.` 符号简化操作多维数组
     *
     * @param  \ArrayAccess|array   $array
     * @param  string               $key
     * @param  mixed                $default
     *
     * @return mixed
     */
    public static function get($array, string $key, $default = null)
    {
        if (! Assert::array($array)) {
            return $default;
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (Assert::array($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * 获得过滤后的数组
     *
     * @param  \ArrayAccess|array   $array
     * @param  callable             $callback
     *
     * @return array
     */
    public static function filter($array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * 根据键值删除数组项
     *
     * @param  \ArrayAccess|array   $array
     * @param  mixed                $value
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function removeByValue($array, $value)
    {
        if (! Assert::array($array)) {
            throw new InvalidArgumentException(
                '`Arr::removeByValue` first parameter must be `array` or implements interface `ArrayAccess`'
            );
        }

        foreach ($array as $i => $val) {
            if ($val === $value) {
                unset($array[$i]);
            }
        }

        return $array;
    }

    /**
     * 根据指定键名对数组分组
     *
     * @param  \ArrayAccess|array   $array
     * @param  string               $key
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function groupBy($array, string $key)
    {
        if (! Assert::array($array)) {
            throw new InvalidArgumentException(
                '`Arr::groupBy` first parameter must be `array` or implements interface `ArrayAccess`'
            );
        }

        $grouped = [];

        foreach ($array as $row) {
            $k = $row[$key];
            $grouped[$k] = $row;
            unset($k);
        }

        return $grouped;
    }

    /**
     * 将一个多维数组转换为键值映射数组
     *
     * @param  \ArrayAccess|array   $array
     * @param  string               $key
     * @param  string               $value
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function hashMap($array, $key, $value = null)
    {
        if (! Assert::array($array)) {
            throw new InvalidArgumentException(
                '`Arr::hashMap` first parameter must be `array` or implements interface `ArrayAccess`'
            );
        }

        $hashMap = [];

        if ($value) {
            foreach ($array as $row) {
                $hashMap[$row[$key]] = $row[$value];
            }
        } else {
            foreach ($array as $row) {
                $hashMap[$row[$key]] = $row;
            }
        }

        return $hashMap;
    }

    /**
     * 将 stdClass 转换为数组
     *
     * @param  stdClass $object
     *
     * @return array
     */
    public static function convertFromStdClass(stdClass $object)
    {
        return Json::decode(Json::encode($object));
    }

    /**
     * 将数组转换为 stdClass
     *
     * @param  \ArrayAccess|array   $array
     *
     * @return \stdClass
     * @throws \InvalidArgumentException
     */
    public static function convertToStdClass($array)
    {
        if (! Assert::array($array)) {
            throw new InvalidArgumentException(
                '`Arr::convertToStdClass` first parameter must be `array` or implements interface `ArrayAccess`'
            );
        }

        return json_decode(Json::encode($array));
    }
}
