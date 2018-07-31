<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Example\Events
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Example\Events;

use One\Event\Event;
use One\Event\Listener;

class AcmeListener extends Listener
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->setHandler([$this, 'listener']);
    }

    /**
     * 监听器
     *
     * @param \One\Event\Event $event
     */
    public function listener(Event $event)
    {
        error_log(
            $event->context->get(0)->getRequestTarget() . PHP_EOL,
            3,
            RUNTIME_PATH . '/logs/example.log'
        );
    }
}
