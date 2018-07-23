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

use One\Swoole\Server as AbstractServer;

class Server extends AbstractServer
{
    /**
     * 启动 Server
     */
    public function start()
    {
        $this
            ->createProtocol()
            ->createSwooleServer(
                $this->getConfig('protocol'),
                $this->getConfig('swoole')
            );

        $this->runSwoole();
    }

    /**
     * 停止 Server
     */
    public function stop()
    {
        if ($pid = $this->isRunning()) {
            posix_kill($pid, SIGTERM);
        }
    }
}
