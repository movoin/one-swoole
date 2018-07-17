<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Contracts
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Contracts;

interface Headers
{
    /**
     * 添加头信息
     *
     * @param string $key
     * @param mixed  $value
     */
    public function add(string $key, $value);

    /**
     * 标准化键名
     *
     * @param  string $key
     *
     * @return string
     */
    public function normalizeKey(string $key): string;
}
