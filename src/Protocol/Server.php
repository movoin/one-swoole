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

use One\Config\Config;
use One\Protocol\Factory;
use One\Swoole\Server as AbstractServer;
use Swoole\Server as SwServer;
use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;

class Server extends AbstractServer
{
    /**
     * 核心组件
     *
     * @var array
     */
    protected $core = [
        'One\\Logging\\Provider',
        'One\\Protocol\\Providers\\Environment',
        'One\\Protocol\\Providers\\ExceptionHandler',
    ];
    /**
     * 基础组件
     *
     * @var array
     */
    protected $providers = [
        'One\\Event\\Provider',
        'One\\FileSystem\\Provider',
        'One\\Annotation\\Provider',
        'One\\Routing\\Provider',
        'One\\Middleware\\Provider',
        'One\\Validation\\Provider',
        'One\\Swoole\\Components\\Task\\Provider',
        'One\\Swoole\\Components\\Timer\\Provider',
    ];

    /**
     * 启动 Server
     */
    public function start()
    {
        if ($this->isRunning() === false) {
            $this
                ->createProtocol(
                    $this->getConfig('protocol')
                )
                ->createSwooleServer(
                    $this->getConfig('protocol'),
                    $this->getConfig('')
                )
                ->initializeCoreProviders()
                ->initializeProviders()
            ;

            // {{ log
            $this->get('logger')->info(
                sprintf(
                    '启动服务 %s',
                    $this->getConfig('listen')
                )
            );
            // }}

            $this->bootServerStartItems();
            $this->runSwoole();
        }
    }

    /**
     * 停止 Server
     */
    public function stop()
    {
        // {{
        $this->initializeCoreProviders();
        // }}

        if ($this->isRunning()) {
            $pid = $this->getPid();

            // {{ log
            $this->get('logger')->info(
                sprintf(
                    '停止服务 %s',
                    $this->getConfig('listen')
                ),
                [
                    'pid' => $pid
                ]
            );
            // }}

            $this->shutdownServerStartItems();

            posix_kill($pid, SIGTERM);
        }
    }

    // ------------ EVENTS START ------------ //

    /**
     * 主进程启动
     *
     * @param  \Swoole\Server   $server
     */
    public function onMasterStart(SwServer $server)
    {
        // {{
        $this->setRunUser();
        $this->setProcessName('master');
        // }}

        // {{ log
        $this->get('logger')->info('启动 Master 进程', [
            'pid' => $server->master_pid
        ]);
        // }}
    }

    /**
     * 主进程结束
     *
     * @param  \Swoole\Server   $server
     */
    public function onMasterStop(SwServer $server)
    {
        // {{ log
        $this->get('logger')->info('结束 Master 进程');
        // }}
    }

    /**
     * 管理进程启动
     *
     * @param  \Swoole\Server   $server
     */
    public function onManagerStart(SwServer $server)
    {
        // {{
        $this->setRunUser();
        $this->setProcessName('manager');
        // }}

        // {{ log
        $this->get('logger')->info('启动 Manager 进程', [
            'pid' => $server->manager_pid
        ]);
        // }}
    }

    /**
     * 工作进程启动
     *
     * @param  \Swoole\Server   $server
     * @param  int              $workerId
     */
    public function onWorkerStart(SwServer $server, int $workerId)
    {
        $workerType = $server->taskworker ? 'task' : 'worker';

        // {{
        $this->setRunUser();
        $this->setProcessName($workerType . ' #' . $workerId);
        // }}

        // {{ log
        $this->get('logger')->info('启动 Worker 进程', [
            'id' => $workerId,
            'pid' => $server->worker_pid,
            'type' => ucfirst($workerType)
        ]);
        // }}

        if ($workerType === 'worker') {
            // {{
            $this->bootWorkerStartItems($workerId);
            // }}
            $this->callProtocolMethod('onStart', $server, $workerId);
        }

        unset($workerType);
    }

    /**
     * 工作进程结束
     *
     * @param  \Swoole\Server   $server
     * @param  int              $workerId
     */
    public function onWorkerStop(SwServer $server, int $workerId)
    {
        $workerType = $server->taskworker ? 'task' : 'worker';

        // {{ log
        $this->get('logger')->info('结束 Worker 进程', [
            'id' => $workerId,
            'type' => ucfirst($workerType)
        ]);
        // }}

        if ($workerType === 'worker') {
            $this->shutdownWorkerStartItems();
            $this->callProtocolMethod('onStop', $server, $workerId);
        }

        unset($workerType);
    }

    /**
     * 工作进程报错
     *
     * @param  \Swoole\Server   $server
     * @param  int              $workerId
     * @param  int              $workerPid
     * @param  int              $exitCode
     */
    public function onWorkerError(SwServer $server, int $workerId, int $workerPid, int $exitCode)
    {
        $workerType = $server->taskworker ? 'task' : 'worker';

        // {{ log
        $this->get('logger')->info('Worker 进程异常退出', [
            'id' => $workerId,
            'pid' => $workerPid,
            'type' => ucfirst($workerType),
            'exitcode' => $exitCode,
        ]);
        // }}

        unset($workerType);

        $this->callProtocolMethod('onError', $server, $workerId);
    }

    /**
     * 工作进程退出
     *
     * @param  \Swoole\Server   $server
     * @param  int              $workerId
     */
    public function onWorkerExit(SwServer $server, int $workerId)
    {
        // {{ log
        $this->get('logger')->info('Worker 进程重启退出', [
            'id' => $workerId
        ]);
        // }}

        $this->callProtocolMethod('onExit', $server, $workerId);
    }

    /**
     * 接收管道消息
     *
     * @param  \Swoole\Server   $server
     * @param  int              $fromId
     * @param  string           $message
     */
    public function onPipeMessage(SwServer $server, int $fromId, string $message)
    {
        // {{ log
        $this->get('logger')->info('接收管道消息', [
            'fromWorkerId' => $fromId,
            'message' => $message
        ]);
        // }}

        $this->callProtocolMethod('onPipeMessage', $server, $fd, $fromId, $data);
    }

    /**
     * 连接接入
     *
     * @param  \Swoole\Server   $server
     * @param  int              $fd
     * @param  int              $reactorId
     */
    public function onConnect(SwServer $server, int $fd, int $reactorId)
    {
        // {{ log
        $this->get('logger')->info('连接接入', $server->getClientInfo($fd));
        // }}

        $this->callProtocolMethod('onConnect', $server, $fd, $fromId);
    }

    /**
     * 连接关闭
     *
     * @param  \Swoole\Server   $server
     * @param  int              $fd
     * @param  int              $fromId
     */
    public function onClose(SwServer $server, int $fd, int $fromId)
    {
        // {{ log
        $this->get('logger')->info('连接关闭', $server->getClientInfo($fd));
        // }}

        $this->callProtocolMethod('onClose', $server, $fd, $fromId);
    }

    /**
     * 接收 TCP 数据
     *
     * @param  \Swoole\Server   $server
     * @param  int              $fd
     * @param  int              $fromId
     * @param  string           $data
     */
    public function onReceive(SwServer $server, int $fd, int $fromId, string $data)
    {
        // {{ log
        $this->get('logger')->info('接收数据', [
            'type' => 'TCP',
            'client' => $server->getClientInfo($fd),
            'data' => $data
        ]);
        // }}

        $this->callProtocolMethod('onReceive', $server, $fd, $fromId, $data);
    }

    /**
     * 接收 UDP 数据
     *
     * @param  \Swoole\Server   $server
     * @param  string           $data
     * @param  array            $clientInfo
     */
    public function onPacket(SwServer $server, string $data, array $clientInfo)
    {
        // {{ log
        $this->get('logger')->info('接收数据', [
            'type' => 'UDP',
            'client' => $clientInfo,
            'data' => $data
        ]);
        // }}

        $this->callProtocolMethod('onPacket', $server, $data, $clientInfo);
    }

    /**
     * 接收 HTTP 请求
     *
     * @param  \Swoole\Http\Request  $swRequest
     * @param  \Swoole\Http\Response $swResponse
     */
    public function onRequest(SwRequest $swRequest, SwResponse $swResponse)
    {
        $request = Factory::newHttpRequest($swRequest);
        $response = Factory::newHttpResponse($swResponse);

        // {{ log
        $this->get('logger')->info('接收数据', [
            'type' => 'HTTP',
            'method' => $request->getMethod(),
            'uri' => $request->getRequestTarget(),
            'client' => $request->getClientIP()
        ]);
        // }}

        // {{
        $this->callProtocolMethod('onRequest', $request, $response);
        // }}

        unset($request, $response);
    }

    /**
     * 投递任务
     *
     * @param  \Swoole\Server   $server
     * @param  int              $taskId
     * @param  int              $fromId
     * @param  mixed            $data
     *
     * @return array
     */
    public function onTask(SwServer $server, int $taskId, int $fromId, $data)
    {
        // {{ log
        $this->get('logger')->info('投递任务', [
            'id' => $taskId,
            'fromId' => $fromId,
            'data' => is_array($data) ? json_encode($data) : $data
        ]);
        // }}

        // {{
        return $this->get('task')->onTask($server, $taskId, $fromId, $data);
        // }}
    }

    /**
     * 结束任务
     *
     * @param  \Swoole\Server   $server
     * @param  int              $taskId
     * @param  mixed            $data
     */
    public function onFinish(SwServer $server, int $taskId, $data)
    {
        // {{
        $this->get('task')->onFinish($server, $taskId, $data);
        // }}

        // {{ log
        $this->get('logger')->info('结束任务', [
            'id' => $taskId,
            'data' => is_array($data) ? json_encode($data) : $data
        ]);
        // }}
    }

    // ------------- EVENTS END ------------- //

    /**
     * 初始化核心组件
     */
    protected function initializeCoreProviders(): self
    {
        array_walk($this->core, function ($item) {
            $provider = $this->make($item, [$this]);
            $provider->register();
            $provider->boot();

            unset($provider);
        });

        // {{ log
        array_walk($this->core, function ($provider) {
            $this->get('logger')->info('注册核心组件 ' . $provider);
        });
        // }}

        return $this;
    }

    /**
     * 初始化基础组件
     */
    protected function initializeProviders(): self
    {
        array_walk($this->providers, function ($item) {
            $provider = $this->make($item, [$this]);
            $provider->register();
            $provider->boot();

            // {{ log
            $this->get('logger')->info('注册基础组件 ' . $item);
            // }}

            unset($provider);
        });

        return $this;
    }

    /**
     * 启动服务进程启动项
     */
    protected function bootServerStartItems()
    {
        // 协议启动项
        $inherents = $this->getProtocol()->getServerStartItems();
        // 自定义启动项
        $customs = Config::get('startup.server', []);
        $providers = array_merge($customs, $inherents);

        unset($inherents, $customs);

        $this->bootItems('server', $providers);
    }

    /**
     * 关闭服务进程启动项
     */
    protected function shutdownServerStartItems()
    {
        // 协议启动项
        $inherents = $this->getProtocol()->getServerStartItems();
        // 自定义启动项
        $customs = Config::get('startup.server', []);
        $providers = array_merge($customs, $inherents);

        unset($inherents, $customs);

        $this->shutdownItems('server', $providers);
    }

    /**
     * 启动工作进程启动项
     */
    protected function bootWorkerStartItems()
    {
        // 协议启动项
        $inherents = $this->getProtocol()->getWorkerStartItems();
        // 自定义启动项
        $customs = Config::get('startup.worker', []);
        $providers = array_merge($customs, $inherents);

        unset($inherents, $customs);

        $this->bootItems('worker', $providers);
    }

    /**
     * 关闭工作进程启动项
     */
    protected function shutdownWorkerStartItems()
    {
        // 协议启动项
        $inherents = $this->getProtocol()->getWorkerStartItems();
        // 自定义启动项
        $customs = Config::get('startup.worker', []);
        $providers = array_merge($customs, $inherents);

        unset($inherents, $customs);

        $this->shutdownItems('worker', $providers);
    }

    /**
     * 启动自定义启动项
     *
     * @param  string $type
     * @param  array  $items
     */
    protected function bootItems(string $type, array $items)
    {
        if ($items !== []) {
            array_walk($items, function ($item) {
                $provider = $this->make($item, [$this]);
                $provider->register();
                $provider->boot();

                unset($boot);

                // {{ log
                $this->get('logger')->info('加载 ' strtoupper($type) ' 启动项 ' . $item);
                // }}
            });
        }
    }

    /**
     * 关闭自定义启动项
     *
     * @param  string $type
     * @param  array  $items
     */
    protected function shutdownItems(string $type, array $items)
    {
        if ($items !== []) {
            array_walk($items, function ($item) {
                $provider = $this->make($item, [$this]);
                $provider->unregister();

                unset($boot);

                // {{ log
                $this->get('logger')->info('关闭 ' strtoupper($type) ' 启动项 ' . $item);
                // }}
            });
        }
    }
}
