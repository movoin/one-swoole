<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole\Components\Task\Contracts
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole\Components\Task\Contracts;

interface TaskHandler
{
    /**
     * 执行任务
     *
     * @param  array $parameters
     *
     * @return mixed
     */
    public function handle(array $parameters);

    /**
     * 完成任务
     *
     * @param  mixed $result
     */
    public function finish($result);
}
