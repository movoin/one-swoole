<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Config\Commands
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Config\Commands;

use One\Config\Config;
use One\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends Command
{
    /**
     * 配置命令
     */
    protected function configure()
    {
        $this
            ->setName('config:show')
            ->setDescription('显示配置信息')
            ->setHelp('显示配置信息')
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
        // {{
        $this->title('配置信息');
        // }}

        $this->treeView(Config::get(''));
        $this->newLine();

        return 0;
    }

    /**
     * 输出树视图
     *
     * @param array     $config
     * @param integer   $level
     */
    protected function treeView(array $config, int $level = 0)
    {
        foreach ($config as $name => $value) {
            if (is_array($value)) {
                $this->writeln(
                    sprintf(
                        '%s<info>- %s</>:',
                        str_repeat(' ', $level * 4),
                        $name
                    )
                );
                $this->treeView($value, $level++);
            } else {
                $this->writeln(
                    sprintf(
                        '%s<info>- %s</> = %s',
                        str_repeat(' ', $level * 4),
                        $name,
                        $value
                    )
                );
            }
        }
    }
}
