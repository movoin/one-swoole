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

final class DateTime
{
    const MINUTE    = 60;
    const HOUR      = 3600;
    const DAY       = 86400;
    const WEEK      = 604800;
    const MONTH     = 2592000;
    const YEAR      = 31536000;
    const NULL      = '0000-00-00 00:00:00';

    /**
     * 构造工厂
     *
     * @param  \DateTime|string|int $time
     * @param  string               $timezone
     *
     * @return \DateTime
     */
    public static function factory($time = null, string $timezone = null): \DateTime
    {
        if (! $timezone instanceof \DateTimeZone) {
            $timezone = $timezone ?: date_default_timezone_get();
            $timezone = new \DateTimeZone($timezone);
        }

        if ($time instanceof \DateTime) {
            return $time->setTimezone($timezone);
        }

        $datetime = new \DateTime('@' . self::timestamp($time));
        return $datetime->setTimezone($timezone);
    }

    /**
     * 转换时间戳
     *
     * @param  \DateTime|string|int $time
     *
     * @return int
     */
    public static function timestamp($time = null): int
    {
        if ($time instanceof \DateTime) {
            return $time->format('U');
        }

        $time = time();

        if ($time !== null) {
            $time = is_numeric($time) ? (int) $time : strtotime($time);
        }

        return $time;
    }

    /**
     * 获得当前日期
     *
     * @param  string $format
     * @param  string $timezone
     *
     * @return string
     */
    public static function now(string $format = 'Y-m-d H:i:s', string $timezone = null): string
    {
        return self::factory(null, $timezone)->format($format);
    }
}
