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

use One\Config\Config;
use One\Console\Runner;
use One\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    /**
     * 配置命令
     */
    protected function configure()
    {
        $this
            ->setName('server:start')
            ->addArgument('server', InputArgument::OPTIONAL, '服务进程名称')
            ->setDescription('启动服务进程')
            ->setHelp('启动指定或全部服务进程')
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
            $this->error('启动失败, 未定义 [' . $server . '] 服务');
            return 0;
        } elseif ($server === null && $servers === []) {
            $this->error('启动失败, 未定义任何服务');
            return 0;
        }

        // {{
        $this->title('启动服务进程');
        // }}

        if ($server !== null) {
            $servers = (array) $server;
        }

        unset($server);

        $runner = new Runner($output);

        try {
            foreach ($servers as $server) {
                if ($runner->isRunning($server)) {
                    $this->fail('<label>' . $server . '</> 处于运行中');
                } else {
                    $ret = $runner->runCommand('start', $server);

                    $this->result(
                        '启动 <label>' . $server . '</> 服务进程',
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
