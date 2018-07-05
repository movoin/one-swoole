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

use ReflectionClass;

final class Reflection
{
    /**
     * 创建实例
     *
     * @param  string $abstract
     * @param  array  $parameters
     *
     * @return object
     */
    public static function newInstance(string $abstract, array $parameters = [])
    {
        $reflector = new ReflectionClass($abstract);

        return $reflector->newInstanceArgs($parameters);
    }
}
