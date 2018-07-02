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

use DateTime as PhpDateTime;
use DateTimeZone;

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
     * @param  mixed $time
     * @param  mixed $timezone
     *
     * @return \DateTime
     */
    public static function factory($time = null, $timezone = null): \DateTime
    {
        if (! $timezone instanceof DateTimeZone) {
            $timezone = $timezone ?: date_default_timezone_get();
            $timezone = new DateTimeZone($timezone);
        }

        if ($time instanceof PhpDateTime) {
            return $time->setTimezone($timezone);
        }

        $datetime = new PhpDateTime('@' . self::timestamp($time));
        return $datetime->setTimezone($timezone);
    }

    /**
     * 转换时间戳
     *
     * @param  \DateTime|string $time
     *
     * @return int
     */
    public static function timestamp($time = null): int
    {
        if ($time instanceof PhpDateTime) {
            return $time->format('U');
        }

        if ($time !== null) {
            $time = is_numeric($time) ? (int) $time : strtotime($time);
        }

        if (! $time) {
            $time = time();
        }

        return $time;
    }

    /**
     * 获得当前日期
     *
     * @param  string $format
     *
     * @return string
     */
    public static function now($format = 'Y-m-d H:i:s'): string
    {
        return self::factory()->format($format);
    }
}
