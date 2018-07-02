<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Support\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Support\Traits;

use One\Support\Container;

trait HasContainer
{
    /**
     * 对象容器
     *
     * @var \One\Support\Container
     */
    private $container;

    /**
     * 设置容器
     *
     * @param \One\Support\Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * 获得容器
     *
     * @return \One\Support\Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
}
