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

use InvalidArgumentException;
use One\Support\Helpers\Assert;

class Listener
{
    /**
     * 事件处理句柄
     *
     * @var \Closure
     */
    protected $handler;

    /**
     * 构造
     *
     * @param \Closure|array $handler
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($handler = null)
    {
        if ($handler !== null) {
            $this->setHandler($handler);
        }
    }

    /**
     * 处理事件
     *
     * @param  \One\Event\Event  $event
     */
    public function handle(Event $event)
    {
        call_user_func_array($this->handler, [$event]);
    }

    /**
     * 获得事件处理句柄
     *
     * @return \Closure
     */
    public function getHandler(): \Closure
    {
        return $this->handler;
    }

    /**
     * 设置事件处理句柄
     *
     * @param  \Closure|array $handler
     *
     * @throws \InvalidArgumentException
     */
    public function setHandler($handler)
    {
        if (Assert::array($handler)) {
            $this->handler = function (Event $event) use ($handler) {
                return call_user_func_array($handler, [$event]);
            };
        } elseif (Assert::instanceOf($handler, '\\Closure')) {
            $this->handler = $handler;
        } else {
            throw new InvalidArgumentException('`Listener::$handler` must be `array` or `Closure`');
        }
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
        if (Assert::instanceOf($listener, '\\One\\Event\\Listener')) {
            $listener = $listener->getHandler();
        }

        return $this->handler === $listener;
    }
}
