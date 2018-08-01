<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Traits;

use One\Swoole\Contracts\Server;

trait HasServer
{
    /**
     * Swoole Server 对象
     *
     * @var \One\Swoole\Contracts\Server
     */
    protected $server;

    /**
     * 获得 Swoole Server 对象
     *
     * @return \One\Swoole\Contracts\Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * 设置 Swoole Server 对象
     *
     * @param \One\Swoole\Contracts\Server $server
     */
    public function setServer(Server $server)
    {
        $this->server = $server;
    }
}
