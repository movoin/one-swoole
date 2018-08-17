<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Routing\Commands
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Routing\Commands;

use One\Annotation\Parser;
use One\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    /**
     * 配置命令
     */
    protected function configure()
    {
        $this
            ->setName('route:list')
            ->setDescription('显示路由规则')
            ->setHelp('显示所有路由规则')
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
        $docs = new Parser(APP_PATH);
        $docs->parse();

        // {{
        $this->title('所有路由规则');
        // }}

        foreach ($docs as $class => $doc) {
            foreach ((array) $doc['methods'] as $method) {
                $this->writeln(
                    sprintf(
                        ' <info>-</> <success>%s</> <title>%s</> <info>-></> %s',
                        str_pad(strtoupper($method), 4),
                        str_pad($doc['route'], 30),
                        $class
                    )
                );
            }
        }

        $this->newLine();

        return 0;
    }
}
