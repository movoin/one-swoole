<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole\Commands
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole\Commands;

use One\Config;
use One\Console\Runner;
use One\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopCommand extends Command
{
    /**
     * 配置命令
     */
    protected function configure()
    {
        $this
            ->setName('server:stop')
            ->addArgument('server', InputArgument::OPTIONAL, '服务进程名称')
            ->setDescription('关闭服务进程')
            ->setHelp('关闭指定或全部服务进程')
        ;
    }

    /**
     * 执行命令
     *
     * @param  \Symfony\Component\Console\Input\InputInterface      $input
     * @param  \Symfony\Component\Console\Output\OutputInterface    $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // 读取所有服务
        $servers = array_keys(Config::get('server', []));
        // 当前服务
        $server = $input->getArgument('server');

        if ($server !== null && ! isset($servers[$server])) {
            $this->error('关闭失败, 未定义 [' . $server . '] 服务');
            return 0;
        } elseif ($server === null && $servers === []) {
            $this->error('关闭失败, 未定义任何服务');
            return 0;
        }

        // {{
        $this->title('关闭服务进程');
        // }}

        if ($server !== null) {
            $servers = (array) $server;
        }

        unset($server);

        $runner = new Runner($output);

        try {
            foreach ($servers as $server) {
                if (! $runner->isRunning($server)) {
                    $this->fail('<label>' . $server . '</> 服务进程未启动');
                } else {
                    $ret = $runner->runCommand('stop', $server);

                    $this->result(
                        '关闭 <label>' . $server . '</> 服务进程',
                        $ret['code'] === 0
                    );
                }

                $this->wait();
            }
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->newLine();

        return 0;
    }
}
