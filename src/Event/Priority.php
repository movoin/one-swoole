<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Event
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Event;

abstract class Priority
{
    /**
     * 低优先级
     */
    const LOW       = 0;
    /**
     * 中优先级
     */
    const NORMAL    = 10;
    /**
     * 高优先级
     */
    const HIGH      = 100;
}
