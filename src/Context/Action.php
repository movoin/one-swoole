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

abstract class Action implements ActionInterface
{
    use HasServer
        HasServerContainer;

    /**
     * 构造
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->setServer($server);
        $this->init();
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
     * 初始化
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
    abstract protected function run(Request $request): PayloadInterface;
}
