<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Console
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Console;

use One\Config;
use One\Protocol\Server;
use Swoole\Process;
use Symfony\Component\Console\Logger\ConsoleLogger;

class Runner
{
    /**
     * 日志对象
     *
     * @var \Symfony\Component\Console\Logger\ConsoleLogger
     */
    protected $logger;

    /**
     * 构造
     */
    public function __construct($output = null)
    {
        Config::load();

        if ($output !== null) {
            $this->logger = new ConsoleLogger($output);
        }
    }

    /**
     * 执行命令
     *
     * @param  array $cmds
     *
     * @return array
     */
    public function runCommand(...$cmds): array
    {
        $this->logger->notice('执行服务命令: ' . implode(' ', $cmds));

        $command = array_merge([
                Config::get('run_command'),
                'server:run'
            ], $cmds);


        $process = new Process(function ($worker) use ($command) {
            $worker->exec(Config::get('php_bin', 'php'), $command);
        }, true);

        $process->start();

        $this->logger->debug(
            '命令: ' . Config::get('php_bin', 'php') . implode(' ', $command)
        );

        $recv = Process::wait();

        $this->logger->debug('结果: ' . json_encode($recv));

        return $recv;
    }

    /**
     * 判断服务是否运行
     *
     * @param  string $server
     *
     * @return bool
     */
    public function isRunning(string $server): bool
    {
        return (new Server(Config::get('name'), $server))->isRunning();
    }
}
