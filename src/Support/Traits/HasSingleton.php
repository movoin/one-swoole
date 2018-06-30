<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Support\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Support\Traits;

trait HasSingleton
{
    /**
     * 单例实例
     *
     * @var self
     */
    private static $singleton = null;

    /**
     * 获得对象单例实例
     *
     * @return self
     */
    final public static function singleton()
    {
        if (static::$singleton === null) {
            static::$singleton = new static();
        }

        return static::$singleton;
    }
}
