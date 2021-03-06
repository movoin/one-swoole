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

interface Arrayable
{
    /**
     * 获得数组
     *
     * @return array
     */
    public function toArray(): array;
}
