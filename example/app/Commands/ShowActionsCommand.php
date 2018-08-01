<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Example\Commands
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Example\Commands;

use One\Console\Command;
use One\FileSystem\Finder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowActionsCommand extends Command
{
    /**
     * 配置命令
     */
    protected function configure()
    {
        $this
            ->setName('show:actions')
            ->setDescription('显示所有方法')
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
        $actions = new Finder(APP_PATH, 'One\\Context\\Contracts\\Action', 'php');

        foreach ($actions as $action) {
            $this->info($action->getBasename('.php'));
        }
    }
}
