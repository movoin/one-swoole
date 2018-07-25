<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Logging
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Logging;

use One\Swoole\Provider;

class LoggerProvider extends Provider
{
    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
        $this->bind('logger', function ($server) {
            $name = $server->getProcessName();
            $path = $this->config('runtime_path', '') . '/logs/' . $name . '.log';

            return new Logger($name, $path);
        });
    }
}
