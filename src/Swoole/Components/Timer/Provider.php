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

use One\Swoole\Provider as AbstractProvider;

class Provider extends AbstractProvider
{
    /**
     * 注册服务
     */
    public function register()
    {
        $this->bind('timer', function ($server) {
            $scheduler = new Scheduler;
            $scheduler->setSwoole($server->getSwoole());

            return $scheduler;
        });
    }

    /**
     * 启动服务
     */
    public function boot()
    {
        $timer = $this->provider('timer');
        $crons = $this->config('cron', []);

        // foreach ($crons as $type => $handler) {
        //     $scheduler->push($name, $handler);
        // }

        unset($crons, $timer);
    }
}
