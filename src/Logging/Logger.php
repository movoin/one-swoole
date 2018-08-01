<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Logging
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Logging;

use Psr\Log\AbstractLogger;
use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;

class Logger extends AbstractLogger
{
    /**
     * 日志频道名称
     *
     * @var string
     */
    private $name;
    /**
     * 日志文件路径
     *
     * @var string
     */
    private $logfile;
    /**
     * 日志操作句柄
     *
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * 构造
     *
     * @param string $name
     * @param string $logfile
     */
    public function __construct(string $name, string $logfile)
    {
        $this->name = strtoupper($name);
        $this->logfile = $logfile;
    }

    /**
     * 写入日志
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = [])
    {
        $this->getLogger()->$level($message, $context);
    }

    /**
     * 获得日志操作句柄
     *
     * @return \Monolog\Logger
     */
    public function getLogger(): MonoLogger
    {
        if ($this->logger === null) {
            $this->logger = new MonoLogger($this->name);
            $this->logger->pushHandler(new StreamHandler($this->logfile));
        }

        return $this->logger;
    }
}
