<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

final class Run
{
    /**
     * 日志对象
     *
     * @var \Psr\Log\LoggerInterface
     */
    private static $logger;

    /**
     * 运行 Shell
     *
     * @param array|string  $sh
     * @param string|null   $cwd
     * @param int           $timeout
     * @param bool          $async
     *
     * @return mixed
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public static function shell($sh, $cwd = null, int $timeout = 60, bool $async = false)
    {
        $process = new Process($sh, $cwd, null, null, $timeout);

        if ($async) {
            $process->start();
            $process->wait(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    static::log('error', $buffer);
                }
            });
        } else {
            $process->run();

            if (! $process->isSuccessful()) {
                static::log('error', $process->getErrorOutput());
                throw new ProcessFailedException($process);
            }

            return $process->getOutput();
        }

        unset($process);
    }

    /**
     * 设置日志对象
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        static::$logger = $logger;
    }

    /**
     * 写入日志
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    private static function log(string $level, string $message, array $context = [])
    {
        if (static::$logger !== null) {
            static::$logger->$level($message, $context);
        }
    }
}
