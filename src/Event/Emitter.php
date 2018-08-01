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

use Closure;
use InvalidArgumentException;
use One\Support\Helpers\Assert;
use One\Support\Helpers\Reflection;

/**
 * 事件发射类
 *
 * @package     One\Event
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */
class Emitter
{
    /**
     * 事件监听句柄
     *
     * @var array
     */
    protected $listeners = [];
    /**
     * 已排序事件监听句柄
     *
     * @var array
     */
    protected $sortedListeners = [];

    /**
     * 构造
     */
    public function __construct()
    {
        $this->listeners = [];
        $this->sortedListeners = [];
    }

    /**
     * 注册事件监听句柄
     *
     * @param  \One\Event\Event|string                      $event
     * @param  \Closure|\One\Event\Listener|array|string    $listener
     * @param  int                                          $priority
     *
     * @throws \InvalidArgumentException
     */
    public function on($event, $listener, int $priority = Priority::NORMAL)
    {
        $this->addListener($event, $listener, $priority);
    }

    /**
     * 注册一次性事件监听句柄
     *
     * @param  \One\Event\Event|string                      $event
     * @param  \Closure|\One\Event\Listener|array|string    $listener
     * @param  int                                          $priority
     *
     * @throws \InvalidArgumentException
     */
    public function once($event, $listener, int $priority = Priority::NORMAL)
    {
        $this->addListener($event, $listener, $priority, true);
    }

    /**
     * 注销事件监听
     *
     * @param  \One\Event\Event|string  $event
     *
     * @throws \InvalidArgumentException
     */
    public function off($event)
    {
        $event = $this->getEventName($event);

        if ($this->hasListener($event)) {
            unset($this->sortedListeners[$event], $this->listeners[$event]);
        }

        unset($event);
    }

    /**
     * 触发事件
     *
     * @param  \One\Event\Event|string  $event
     * @param  array                    $parameters
     *
     * @throws \InvalidArgumentException
     */
    public function emit($event, ...$parameters)
    {
        $event = $this->ensureEvent($event);
        $event->setEmitter($this);
        $event->setContexts($parameters);

        $listeners = $this->getListeners($event);

        foreach ($listeners as $listener) {
            $listener->handle($event);
        }

        unset($listeners, $event);
    }

    /**
     * 获得指定事件的监听器
     *
     * @param  \One\Event\Event|string  $event
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getListeners($event): array
    {
        $event = $this->getEventName($event);

        if (! $this->hasListener($event)) {
            return [];
        }

        if (! array_key_exists($event, $this->sortedListeners)) {
            $listeners = $this->listeners[$event];

            krsort($listeners);

            $listeners = call_user_func_array(
                'array_merge',
                $listeners
            );
            $this->sortedListeners[$event] = $listeners;

            unset($listeners);
        }

        return $this->sortedListeners[$event];
    }

    /**
     * 判断指定事件是否存在监听器
     *
     * @param  \One\Event\Event|string  $event
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function hasListener($event): bool
    {
        $name = $this->getEventName($event);

        if (isset($this->listeners[$name])) {
            foreach ($this->listeners[$name] as $listeners) {
                if (count($listeners) > 0) {
                    return true;
                }
            }
        }

        unset($name);

        return false;
    }

    /**
     * 添加事件监听
     *
     * @param  \One\Event\Event|string                      $event
     * @param  \Closure|\One\Event\Listener|array|string    $listener
     * @param  int                                          $priority
     * @param  bool                                         $once
     *
     * @throws \InvalidArgumentException
     */
    public function addListener($event, $listener, int $priority = Priority::NORMAL, $once = false)
    {
        $name = $this->getEventName($event);

        if (! $this->hasListener($name)) {
            $this->listeners[$name] = [];
        }

        if (! isset($this->listeners[$name][$priority])) {
            $this->listeners[$name][$priority] = [];
        }

        $listener = $once ? $this->ensureOnceListener($listener) : $this->ensureListener($listener);

        $this->listeners[$name][$priority][] = $listener;

        unset($name, $listener);
    }

    /**
     * 移除事件监听
     *
     * @param  \One\Event\Event|string                      $event
     * @param  \Closure|\One\Event\Listener|array|string    $listener
     *
     * @throws \InvalidArgumentException
     */
    public function removeListener($event, $listener)
    {
        if (! $this->hasListener($event)) {
            return;
        }

        $event = $this->getEventName($event);
        $listeners = $this->listeners[$event];

        foreach ($listeners as $priority => $registed) {
            $listeners[$priority] = array_filter($registed, function ($reg) use ($listener) {
                return ! $reg->isListener($listener);
            });
        }

        $this->listeners[$event] = $listeners;

        unset($this->sortedListeners[$event], $listeners, $event);
    }

    /**
     * 获得事件名称
     *
     * @param  \One\Event\Event|string  $event
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getEventName($event)
    {
        if (Assert::instanceOf($event, '\\One\\Event\\Event')) {
            return $event->getName();
        } elseif (Assert::string($event)) {
            return $event;
        }

        throw new InvalidArgumentException(
            '`Event` must be `string` or extends class `One\Event\Event`'
        );
    }

    /**
     * 创建事件实例
     *
     * @param  \One\Event\Event|string  $event
     *
     * @return \One\Event\Event
     * @throws \InvalidArgumentException
     */
    protected function ensureEvent($event): Event
    {
        if (Assert::instanceOf($event, '\\One\\Event\\Event')) {
            return $event;
        } elseif (Assert::string($event)) {
            return new Event($event);
        }

        throw new InvalidArgumentException(
            '`Event` must be `string` or extends class `One\Event\Event`'
        );
    }

    /**
     * 创建事件监听器实例
     *
     * @param  \Closure|\One\Event\Listener|array|string   $listener
     *
     * @return \One\Event\Listener
     * @throws \InvalidArgumentException
     */
    protected function ensureListener($listener): Listener
    {
        if (Assert::instanceOf($listener, '\\One\\Event\\Listener')) {
            return $listener;
        } elseif (Assert::instanceOf($listener, '\\Closure') || Assert::array($listener)) {
            return new Listener($listener);
        } elseif (Assert::string($listener)) {
            return Reflection::newInstance($listener);
        }

        throw new InvalidArgumentException(
            '`Listener` must be `array`, `Closure` or extends class `One\Event\Listener`'
        );
    }

    /**
     * 创建一次性事件监听器实例
     *
     * @param  \Closure|\One\Event\OnceListener|array|string   $listener
     *
     * @return \One\Event\OnceListener
     * @throws \InvalidArgumentException
     */
    protected function ensureOnceListener($listener): OnceListener
    {
        if (Assert::instanceOf($listener, '\\One\\Event\\OnceListener')) {
            return $listener;
        } elseif (Assert::instanceOf($listener, '\\Closure') || Assert::array($listener)) {
            return new OnceListener($listener);
        } elseif (Assert::string($listener)) {
            return Reflection::newInstance($listener);
        }

        throw new InvalidArgumentException(
            '`OnceListener` must be `array`, `Closure` or extends class `One\Event\OnceListener`'
        );
    }
}
