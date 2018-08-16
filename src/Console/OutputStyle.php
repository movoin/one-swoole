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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

class OutputStyle
{
    /**
     * Symfony Style
     *
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $style;
    /**
     * 输出对象
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * 构造
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->style = new SymfonyStyle($input, $output);
        $this->output = $output;
    }

    /**
     * 输出标题
     *
     * @param  string $message
     */
    public function title(string $message)
    {
        $this->style->newLine();
        $this->output->writeln(sprintf(
            '<highlight>>></> <title>%s</>',
            OutputFormatter::escapeTrailingBackslash(strtoupper($message))
        ));
        $this->style->newLine();
    }

    /**
     * 输出段落
     *
     * @param  string $message
     */
    public function section(string $message)
    {
        $this->style->newLine();
        $this->output->writeln(sprintf(
            '<info>>></> <title>%s</>',
            OutputFormatter::escapeTrailingBackslash($message)
        ));
        $this->style->newLine();
    }

    /**
     * 输出结果
     *
     * @param string $message
     * @param bool   $isOk
     */
    public function result(string $message, bool $isOk)
    {
        if ($isOk) {
            $this->ok($message);
        } else {
            $this->fail($message);
        }
    }

    /**
     * 输出成功信息
     *
     * @param string $message
     * @param string $type
     */
    public function ok(string $message, string $type = 'info')
    {
        $this->output($type, 'success', $message);
    }

    /**
     * 输出失败信息
     *
     * @param string $message
     * @param string $type
     */
    public function fail(string $message, string $type = 'erro')
    {
        $this->output($type, 'failure', $message);
    }

    /**
     * 输出格式文字
     *
     * @param string $type
     * @param string $style
     * @param string $message
     * @param mixed  $status
     */
    public function output(string $type, string $style, string $message, $status = true)
    {
        $text = '....';

        if ($status === true) {
            $text .= '<success>OK</>';
        } elseif ($status === false) {
            $text = '';
        } else {
            $text .= "<info>{$status}</>";
        }

        $type = "<{$style}>{$type}</>";

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
     * 输出格式行
     *
     * formatLine(
     *     '<foo> > <bar>',
     *     [
     *         'foo' => 'value',
     *         'bar' => 'value'
     *     ],
     *     [
     *         'foo' => 'info',
     *         'bar' => 'highlight'
     *     ]
     * );
     *
     * @param string $format
     * @param array  $item
     * @param array  $style
     */
    public function formatLine(string $format, array $item, array $style = [])
    {
        foreach ($item as $key => $value) {
            $value = (isset($style[$key]) && ! empty($style[$key])) ?
                    "<{$style[$key]}>{$value}</>" :
                    $value;
            $format = str_replace("<{$key}>", $value, $format);
        }

        $this->output->writeln($format);
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
     * __call
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->style, $method], $args);
    }
}
