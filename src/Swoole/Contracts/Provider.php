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

use One\Swoole\Contracts\Server;

interface Provider
{
    /**
     * 构造
     *
     * @param \One\Swoole\Contracts\Server $server
     */
    public function __construct(Server $server);

    /**
     * 注册服务
     */
    public function register();

    /**
     * 启动服务
     */
    public function boot();
}
