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

use One\Support\Helpers\Assert;

class OnceListener extends Listener
{
    /**
     * 处理事件
     *
     * @param  \One\Event\Event  $event
     */
    public function handle(Event $event)
    {
        $name = $event->getName();
        $emitter = $event->getEmitter();
        $emitter->removeListener($name, $this);

        unset($name, $emitter);

        call_user_func_array($this->handler, [$event]);
    }

    /**
     * 判断是否是自己
     *
     * @param  mixed $listener
     *
     * @return bool
     */
    public function isListener($listener): bool
    {
        if (Assert::instanceOf($listener, '\\One\\Event\\OnceListener')) {
            $listener = $listener->getHandler();
        }

        return $this->handler === $listener;
    }
}
