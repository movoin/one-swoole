<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Traits;

trait HasStartItem
{
    /**
     * 服务进程启动项目
     *
     * @var array
     */
    protected $serverStartItems = [];
    /**
     * 工作进程启动项目
     *
     * @var array
     */
    protected $workerStartItems = [];

    /**
     * 获得服务进程启动项目
     *
     * @return array
     */
    public function getServerStartItems(): array
    {
        return $this->serverStartItems;
    }

    /**
     * 获得工作进程启动项目
     *
     * @return array
     */
    public function getWorkerStartItems(): array
    {
        return $this->workerStartItems;
    }
}
