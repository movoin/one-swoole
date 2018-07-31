<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Context
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Context;

use One\Context\Contracts\Action as ActionInterface;
use One\Context\Contracts\Payload as PayloadInterface;
use One\Protocol\Contracts\Request;
use One\Protocol\Traits\HasServer;
use One\Protocol\Traits\HasServerContainer;
use One\Swoole\Contracts\Server;

/**
 * 请求响应动作基类
 *
 * 当接到请求时，协议层会将处理好的 `One\Protocol\Contracts\Request` 对象传入 `__invoke()` 方法，
 * 而具体的执行逻辑，则由 `run()` 方法完成。
 *
 * 在运行前可以通过 `init()` 方法对动作初始化。
 *
 * 返回结果必须为 `One\Context\Contracts\Payload` 对象。
 *
 * @package     One\Context
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */
abstract class Action implements ActionInterface
{
    use HasServer,
        HasServerContainer;

    /**
     * 构造
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->setServer($server);
    }

    /**
     * 响应动作请求
     *
     * @param  \One\Protocol\Contracts\Request  $request
     *
     * @return \One\Context\Contracts\Payload
     */
    public function __invoke(Request $request): PayloadInterface
    {
        $this->init();

        // {{ event: action.before
        $this->event->emit('action.before', $request);
        // }}

        $payload = $this->run($request);

        // {{ event: action.after
        $this->event->emit('action.after', $request, $payload);
        // }}

        return $payload;
    }

    /**
     * 初始化方法
     */
    protected function init()
    {
    }

    /**
     * 响应请求
     *
     * @param  \One\Protocol\Contracts\Request  $request
     *
     * @return \One\Context\Contracts\Payload
     */
    abstract protected function run(Request $request);
}
