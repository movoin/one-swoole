<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole\Components\Task
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole\Components\Task;

use One\Swoole\Provider as AbstractProvider;

class Provider extends AbstractProvider
{
    /**
     * 注册服务
     */
    public function register()
    {
        $this->bind('task', function ($server) {
            $scheduler = new Scheduler;
            $scheduler->setSwoole($server->getSwoole());

            // {{ Attach Task Handlers
            $tasks = $this->config('task', []);

            foreach ($tasks as $name => $handler) {
                $scheduler->push($name, $handler);
            }

            unset($tasks);
            // }}

            return $scheduler;
        });
    }
}
