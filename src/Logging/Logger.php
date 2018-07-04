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
use Psr\Log\LoggerAwareTrait;

class Logger extends AbstractLogger
{
    use LoggerAwareTrait;

    /**
     * 写入日志
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->$level($message, $context);
    }
}
