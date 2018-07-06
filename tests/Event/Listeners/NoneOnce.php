<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Event\Listeners
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Event\Listeners;

use One\Event\Event;
use One\Event\OnceListener;

class NoneOnce extends OnceListener
{
    /**
     * 构造
     *
     * @param \Closure|array $handler
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($handler = null)
    {
        $this->setHandler([$this, 'none']);
    }

    public function none(Event $event)
    {
    }
}
