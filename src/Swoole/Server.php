<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole;

use One\Support\Container;
use Swoole\Server as SwServer;
use Swoole\Http\Server as SwHttpServer;
use Swoole\WebSocket\Server as SwWebSocketServer;

class Server extends Container
{
    /**
     * 默认监听 IP
     */
    const DEFAULT_HOST = '0.0.0.0';
    /**
     * 默认监听端口
     */
    const DEFAULT_PORT = 9501;
}
