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

use Swoole\Server as SwServer;
use Swoole\Http\Server as SwHttpServer;
use Swoole\WebSocket\Server as SwWebSocketServer;

trait HasSwoole
{
    /**
     * Swoole Server 对象
     *
     * @var \Swoole\Server
     */
    protected $swoole;

    /**
     * 获得 Swoole Server 对象
     *
     * @return \Swoole\Server
     */
    protected function getSwoole(): SwServer
    {
        return $this->swoole;
    }

    /**
     * 运行 Swoole Server
     */
    protected function runSwoole()
    {
        $this->swoole->start();
    }

    /**
     * 创建 Swoole Server 实例
     *
     * @param  string $protocolName
     * @param  array  $swooleConfig
     *
     * @return \Swoole\Server
     * @throws \InvalidArgumentException
     */
    protected function createSwooleServer(string $protocolName, array $swooleConfig = []): SwServer
    {
        switch ($protocolName) {
            // HTTP Server
            case Protocol::HTTP:
                $server = SwHttpServer::class;
                break;

            // WebSocket Server
            case Protocol::WEBSOCKET:
                $server = SwWebSocketServer::class;
                break;

            // TCP/UDP Server
            default:
                $server = SwServer::class;
                break;
        }

        // {{ 创建 Server
        $swoole = new $server(
            $swooleConfig['host'],
            $swooleConfig['port'],
            SWOOLE_PROCESS,
            SWOOLE_SOCK_TCP
        );

        $swoole->set($swooleConfig['swoole']);
        unset($server);
        // }}

        return $this->bindSwooleEvents($swoole, $protocolName);
    }

    /**
     * 绑定 Swoole Server 事件
     *
     * @param  \Swoole\Server   $swoole
     * @param  string           $protocolName
     *
     * @return \Swoole\Server
     */
    protected function bindSwooleEvents(SwServer $swoole, string $protocolName): SwServer
    {
        // {{ 初始化 Swoole Server 事件
        array_walk([
            'Start'         => 'onMasterStart',
            'Shutdown'      => 'onMasterStop',
            'ManagerStart'  => 'onManagerStart',
            'WorkerStart'   => 'onWorkerStart',
            'WorkerStop'    => 'onWorkerStop',
            'WorkerError'   => 'onWorkerError',
            'WorkerExit'    => 'onWorkerExit',
            'PipeMessage'   => 'onPipeMessage',
            'Connect'       => 'onConnect',
            'Close'         => 'onClose',
            'Receive'       => 'onReceive',
        ], function ($handler, $event) use ($swoole) {
            $swoole->on($event, [$this, $handler]);
        });

        switch ($protocolName) {
            // HTTP Server
            case Protocol::HTTP:
                $swoole->on('Request', [$this, 'onRequest']);
                break;

            // UDP Server
            case Protocol::UDP:
                $swoole->on('Packet', [$this, 'onPacket']);
                break;

            // WebSocket Server
            case Protocol::WEBSOCKET:
                $swoole->on('Open', [$this, 'onOpen']);
                $swoole->on('HandShake', [$this, 'onHandShake']);
                $swoole->on('Message', [$this, 'onMessage']);
                break;
        }

        $tasks = isset($swoole->setting['task_worker_num']) ?
                (int) $swoole->setting['task_worker_num'] :
                0;

        if ($task > 0) {
            $swoole->on('Task', [$this, 'onTask']);
            $swoole->on('Finish', [$this, 'onFinish']);
        }

        unset($tasks);
        // }}

        return $swoole;
    }
}
