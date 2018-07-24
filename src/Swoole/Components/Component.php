<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole\Components
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole\Components;

use Swoole\Server;

class Component
{
    /**
     * Swoole Server 对象
     *
     * @var \Swoole\Server
     */
    private $swoole;

    /**
     * 获得 Swoole Server 对象
     *
     * @return \Swoole\Server
     */
    public function getSwoole(): Server
    {
        return $this->swoole;
    }

    /**
     * 设置 Swoole Server
     *
     * @param \Swoole\Server $swoole
     */
    public function setSwoole(Server $swoole)
    {
        $this->swoole = $swoole;
    }
}
