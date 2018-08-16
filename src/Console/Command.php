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

use One\Console\OutputStyle;
use One\Console\Contracts\Command as CommandInterface;
use One\Support\Helpers\Reflection;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand implements CommandInterface
{
    /**
     * 输出对象
     *
     * @var \One\Console\OutputStyle
     */
    protected $output;

    /**
     * 输出列表项目
     *
     * @param  string $message
     * @param  string $status
     * @param  string $decorateStyle
     * @param  string $decorate
     */
    public function status(string $message, string $status, string $decorateStyle = 'info', string $decorate = '*')
    {
        $this->output->writeln(
            sprintf(
                ' <%s>%s</> %s %s',
                $decorateStyle,
                $decorate,
                str_pad($message, 36),
                $status
            )
        );
    }

    /**
     * __call
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->output, $method], $args);
    }

    /**
     * 初始化
     *
     * @param  \Symfony\Component\Console\Input\InputInterface      $input
     * @param  \Symfony\Component\Console\Output\OutputInterface    $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $styles = [
            // ---------------------------------------
            // Style    => [ Color, BgColor, Options ]
            // ---------------------------------------
            'title'     => [ 'white', null, ['bold'] ],
            'info'      => [ 'cyan' ],
            'warn'      => [ 'yellow', null, ['bold'] ],
            'erro'      => [ 'red', null, ['bold'] ],
            'success'   => [ 'green' ],
            'failure'   => [ 'yellow' ],
            'label'     => [ 'cyan', null, ['underscore'] ],
            'comment'   => [ 'white', null, ['reverse'] ],
            'highlight' => [ 'yellow' ],
        ];

        foreach ($styles as $name => $style) {
            $output->getFormatter()->setStyle(
                $name,
                Reflection::newInstance(
                    OutputFormatterStyle::class,
                    $style
                )
            );
        }

        $this->output = new OutputStyle($input, $output);
    }
}
