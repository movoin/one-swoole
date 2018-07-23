<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Server\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Server\Traits;

use One\Server\Contracts\Server;

trait HasServer
{
    /**
     * Server 对象
     *
     * @var \One\Server\Contracts\Server
     */
    protected $server;

    /**
     * 获得 Server 对象
     *
     * @return \One\Server\Contracts\Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * 设置 Server 对象
     *
     * @param \One\Server\Contracts\Server $server
     */
    public function setServer(Server $server)
    {
        $this->server = $server;
    }
}
