<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Console\Commands\Server
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Console\Commands\Server;

use One\Config;
use One\Protocol\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;

class RunCommand extends Command
{
    /**
     * 配置命令
     */
    protected function configure()
    {
        $this
            ->setName('server:run')
            ->addArgument('cmd', InputArgument::REQUIRED, 'Server command')
            ->addArgument('server', InputArgument::REQUIRED, 'Server process name')
            ->setHidden(true)
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
        $cmd        = $input->getArgument('cmd');
        $process    = $input->getArgument('server');

        $server = new Server(
            Config::get('name'),
            $process
        );

        $server->$cmd();
    }
}
