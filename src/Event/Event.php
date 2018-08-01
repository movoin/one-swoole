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

use One\Support\Traits\HasGetter;

class Event
{
    use HasGetter;

    /**
     * 事件名称
     *
     * @var string
     */
    protected $name;
    /**
     * 事件上下文
     *
     * @var \One\Event\Context
     */
    protected $context;
    /**
     * 事件触发器
     *
     * @var \One\Event\Emitter
     */
    protected $emitter;

    /**
     * 构造事件
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * 获得事件名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 获得事件上下文
     *
     * @return \One\Event\Context
     */
    public function getContext(): Context
    {
        if ($this->context === null) {
            $this->context = new Context;
        }

        return $this->context;
    }

    /**
     * 设置事件上下文
     *
     * @param array $contexts
     */
    public function setContexts($contexts)
    {
        if ($contexts) {
            foreach ((array) $contexts as $name => $value) {
                $this->getContext()->set($name, $value);
            }
        }
    }

    /**
     * 获得事件触发器
     *
     * @return \One\Event\Emitter
     */
    public function getEmitter(): Emitter
    {
        return $this->emitter;
    }

    /**
     * 设置事件触发器
     *
     * @param \One\Event\Emitter $emitter
     */
    public function setEmitter(Emitter $emitter)
    {
        $this->emitter = $emitter;
    }
}
