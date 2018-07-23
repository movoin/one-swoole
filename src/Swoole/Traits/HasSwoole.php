<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole\Traits;

use Swoole\Server as SwServer;

trait HasSwoole
{
    /**
     * Swoole Server 对象
     *
     * @var \Swoole\Server
     */
    protected $swoole;

    /**
     * 获得 Swoole Server 对象
     *
     * @return \Swoole\Server
     */
    public function getSwoole(): SwServer
    {
        return $this->swoole;
    }

    /**
     * 设置 Swoole Server 对象
     *
     * @param \Swoole\Server $swoole
     */
    public function setSwoole(SwServer $swoole)
    {
        $this->swoole = $swoole;
    }

    /**
     * 运行 Swoole Server
     */
    protected function runSwoole()
    {
        $this->swoole->start();
    }
}
