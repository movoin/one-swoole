<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole\Traits;

use One\Protocol\Factory;
use One\Protocol\Contracts\Protocol;

trait HasProtocol
{
    /**
     * 协议对象
     *
     * @var \One\Protocol\Contracts\Protocol
     */
    protected $protocol;

    /**
     * 获得协议
     *
     * @return \One\Protocol\Contracts\Protocol
     */
    protected function getProtocol(): Protocol
    {
        return $this->protocol;
    }

    /**
     * 创建协议
     *
     * @param  string $protocol
     *
     * @return self
     * @throws \One\Protocol\Exceptions\ProtocolException
     */
    protected function createProtocol(string $protocol): self
    {
        $this->protocol = Factory::newProtocol($protocol);
        $this->protocol->setServer($this);

        return $this;
    }

    /**
     * 调用协议方法
     *
     * @param  string $event
     * @param  array  $parameters
     */
    protected function callProtocolMethod(string $event, ...$parameters)
    {
        if (method_exists($this->protocol, $event)) {
            call_user_func_array([$this->protocol, $event], $parameters);
        }
    }
}
