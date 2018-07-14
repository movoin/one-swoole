<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol;

use One\Protocol\Contracts\Protocol;
use One\Protocol\Exceptions\ProtocolException;
use One\Support\Helpers\Assert;

final class Factory
{
    /**
     * 支持协议
     *
     * @var array
     */
    private static $protocols = [];

    /**
     * 创建协议
     *
     * @param  string $protocol
     *
     * @return \One\Protocol\Contracts\Protocol
     * @throws \One\Protocol\Exceptions\ProtocolException
     */
    public static function newProtocol(string $protocol): Protocol
    {
        if (! Assert::oneOf($protocol, static::$protocols)) {
            throw ProtocolException::notSupport($ptotocol);
        }
    }

    /**
     * 创建 HTTP 协议
     *
     * @return \One\Protocol\Contracts\Protocol
     */
    public static function newHttpProtocol()
    {
    }

    /**
     * 创建 TCP 协议
     *
     * @return \One\Protocol\Contracts\Protocol
     */
    public static function newTcpProtocol()
    {
    }

    /**
     * 创建 UDP 协议
     *
     * @return \One\Protocol\Contracts\Protocol
     */
    public static function newUdpProtocol()
    {
    }

    /**
     * 创建 WebSocket 协议
     *
     * @return \One\Protocol\Contracts\Protocol
     */
    public static function newWebSocketProtocol()
    {
    }
}
