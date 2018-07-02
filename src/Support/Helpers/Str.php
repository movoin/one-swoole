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

final class Str
{
    /**
     * 获得字符串长度
     *
     * @param  string $value
     * @param  string $encoding
     *
     * @return int
     */
    public static function length(string $value, string $encoding = null): int
    {
        if ($encoding) {
            return mb_strlen($value, $encoding);
        }

        return mb_strlen($value);
    }

    /**
     * 判断是否由指定字符开头
     *
     * @param  string       $haystack
     * @param  string|array $needles
     *
     * @return bool
     */
    public static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }
}
