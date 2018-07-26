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

use One\Support\Helpers\Reflection;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

class Command extends SymfonyCommand
{
    /**
     * 输入对象
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;
    /**
     * 输出对象
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;
    /**
     * Symfony Style 对象
     *
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $style;

    /**
     * 获得 Symfony Style 对象
     *
     * @return \Symfony\Component\Console\Style\SymfonyStyle
     */
    public function symfony()
    {
        if ($this->style === null) {
            $this->style = new SymfonyStyle($this->input, $this->output);
        }

        return $this->style;
    }

    /**
     * 输出标题
     *
     * @param  string $message
     */
    public function title(string $message)
    {
        $this->newLine();
        $this->output->writeln(sprintf(
            '<highlight>>></> <title>%s</>',
            OutputFormatter::escapeTrailingBackslash(strtoupper($message))
        ));
        $this->newLine();
    }

    /**
     * 输出段落
     *
     * @param  string $message
     */
    public function section(string $message)
    {
        $this->newLine();
        $this->output->writeln(sprintf(
            '<info>>></> <title>%s</>',
            OutputFormatter::escapeTrailingBackslash($message)
        ));
        $this->newLine();
    }

    /**
     * 输出成功状态信息
     *
     * @param  string $message
     * @param  bool   $status
     */
    public function ok(string $message, bool $status = true)
    {
        $this->write('info', $message, $status);
    }

    /**
     * 输出失败信息
     *
     * @param  string $message
     * @param  bool   $status
     */
    public function fail(string $message, bool $status = true)
    {
        $this->write('erro', $message, $status);
    }

    /**
     * 输出信息
     *
     * @param  string $message
     * @param  bool   $status
     */
    public function info(string $message, bool $status = false)
    {
        $this->write('info', $message, $status);
    }

    /**
     * 输出错误信息
     *
     * @param  string $message
     * @param  bool   $status
     */
    public function error(string $message, bool $status = false)
    {
        $this->write('erro', $message, $status);
    }

    /**
     * 输出警告信息
     *
     * @param  string $message
     * @param  bool   $status
     */
    public function warn(string $message, bool $status = false)
    {
        $this->write('warn', $message, $status);
    }

    /**
     * 输出内容
     *
     * @param  string $type
     * @param  string $message
     * @param  bool   $status
     */
    public function write(string $type, string $message, bool $status = true)
    {
        if ($type === 'info') {
            $type = "<info>{$type}</>";
            $text = '....<success>OK</>';
        } else {
            $type = "<{$type}>{$type}</>";
            $text = '....<failure>FAIL</>';
        }

        if ($status === false) {
            $text = '';
        }

        $this->output->writeln(
            sprintf(
                '%s: %s %s',
                $type,
                $message,
                $text
            )
        );
    }

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
     * 输出空行
     *
     * @param  int $count
     */
    public function newLine($count = 1)
    {
        $this->output->write(str_repeat(PHP_EOL, $count));
    }

    /**
     * 等待一定时间
     *
     * @param  int $mstime
     */
    public function wait($mstime = 10000)
    {
        usleep($mstime);
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

        $this->input = $input;
        $this->output = $output;
    }
}
