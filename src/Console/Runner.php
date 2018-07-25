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
use Swoole\Process;

class Runner
{
    /**
     * 构造
     */
    public function __construct()
    {
        Config::load();
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
        $command = array_merge([
                Config::get('run_command'),
                'server:run'
            ], $cmds);

        $process = new Process(function ($worker) use ($command) {
            $worker->exec(Config::get('php_bin', 'php'), $command);
        }, true);

        $process->start();

        $recv = Process::wait();

        return $recv;
    }
}
