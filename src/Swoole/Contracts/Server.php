<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole\Contracts
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole\Contracts;

interface Server
{
    /**
     * 启动 Server
     */
    public function start();

    /**
     * 停止 Server
     */
    public function stop();

    /**
     * 判断服务是否处于运行状态
     *
     * @return bool
     */
    public function isRunning(): bool;
}
