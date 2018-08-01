<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole\Components\Task
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole\Components\Task;

use RuntimeException;
use One\Support\Helpers\Reflection;
use One\Swoole\Components\Component;
use One\Swoole\Components\Task\Contracts\TaskHandler;
use Swoole\Server;

class Scheduler extends Component
{
    /**
     * 是否启用
     *
     * @var bool
     */
    private $enabled = false;
    /**
     * 任务响应句柄
     *
     * @var array
     */
    private $handlers = [];

    /**
     * 设置 Swoole Server
     *
     * @param \Swoole\Server $swoole
     */
    public function setSwoole(Server $swoole)
    {
        $enabled = isset($swoole->setting['task_worker_num']) &&
                    (int) $swoole->setting['task_worker_num'] > 0;

        if ($enable) {
            $this->swoole = $swoole;
        }

        $this->setEnable($enabled);

        unset($enabled);
    }

    /**
     * 设置启用状态
     *
     * @param bool $enable
     */
    public function setEnable(bool $enable)
    {
        $this->enabled = $enable;
    }

    /**
     * 判断是否存在指定任务的响应句柄
     *
     * @param  string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->handlers[$name]) && count($this->handlers[$name]) > 0;
    }

    /**
     * 添加任务响应句柄
     *
     * @param  string $name
     * @param  string $handler
     */
    public function push(string $name, string $handler)
    {
        if (! isset($this->handlers[$name])) {
            $this->handlers[$name] = [];
        }

        if (! in_array($handler, $this->handlers[$name])) {
            $this->handlers[$name][] = $handler;
        }
    }

    /**
     * 运行任务
     *
     * @param  string $name
     * @param  mixed  $parameters
     */
    public function run(string $name, $parameters = null)
    {
        if ($this->enabled && $this->has($name)) {
            $this->getSwoole()->task([
                'name' => $name,
                'parameters' => $parameters
            ]);
        }
    }

    /**
     * 结束任务
     *
     * @param  string     $name
     * @param  array|null $result
     */
    public function finish(string $name, array $result = null)
    {
        if ($this->enabled && $this->has($name)) {
            $this->getSwoole()->finish([
                'name' => $name,
                'result' => $result
            ]);
        }
    }

    /**
     * Swoole 异步任务响应事件
     *
     * @param  \Swoole\Server   $server
     * @param  int              $taskId
     * @param  int              $fromId
     * @param  array            $data
     *
     * @return array
     * @throws \RuntimeException
     */
    public function onTask(Server $server, int $taskId, int $fromId, array $data): array
    {
        $result = [
            'name' => $data['name'],
            'result' => []
        ];

        array_walk($this->handlers[$data['name']], function ($name) use ($result) {
            $handler = $this->createHandler($name);
            $result[$name][] = $handler->handle($data['parameters']);
            unset($handler);
        });

        return $result;
    }

    /**
     * Swoole 异步任务结束事件
     *
     * @param  \Swoole\Server   $server
     * @param  int              $taskId
     * @param  mixed            $data
     *
     * @throws \RuntimeException
     */
    public function onFinish(Server $server, int $taskId, $data)
    {
        array_walk($this->handlers[$data['name']], function ($name) {
            $handler = $this->createHandler($name);
            $handler->finish($data['result']);
            unset($handler);
        });
    }

    /**
     * 创建任务响应句柄
     *
     * @param  string $name
     *
     * @return One\Swoole\Components\Task\Contracts\TaskHandler
     * @throws \RuntimeException
     */
    protected function createHandler(string $name): TaskHandler
    {
        $handler = Reflection::newInstance(ltrim($name, '\\'));

        if ($handler instanceof TaskHandler) {
            return $handler;
        }

        throw new RuntimeException('Task ' . $name . ' handler must implements `TaskHandler` interface');
    }
}
