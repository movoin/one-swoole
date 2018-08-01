<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole\Components\Timer
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole\Components\Timer;

use Closure;
use One\Swoole\Components\Component;

class Scheduler extends Component
{
    /**
     * 添加定时执行器
     *
     * @param  int      $interval
     * @param  \Closure $callback
     * @param  mixed    $parameters
     *
     * @return int
     */
    public function tick(int $interval, Closure $callback, $parameters = null): int
    {
        return $this->getSwoole()->tick($interval, $callback, $parameters);
    }

    /**
     * 添加延后执行器
     *
     * @param  int      $interval
     * @param  \Closure $callback
     * @param  mixed    $parameters
     *
     * @return int
     */
    public function defer(int $interval, Closure $callback, $parameters = null): int
    {
        return $this->getSwoole()->defer($interval, $callback, $parameters);
    }

    /**
     * 添加延时执行器
     *
     * @param  int      $interval
     * @param  \Closure $callback
     * @param  mixed    $parameters
     *
     * @return int
     */
    public function after(int $interval, Closure $callback, $parameters = null): int
    {
        return $this->getSwoole()->after($interval, $callback, $parameters);
    }

    /**
     * 删除指定定时执行器
     *
     * @param  int $id
     *
     * @return bool
     */
    public function clear(int $id): bool
    {
        return $this->getSwoole()->clearTimer($id);
    }
}
