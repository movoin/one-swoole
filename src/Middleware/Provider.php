<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Middleware
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Middleware;

use One\Swoole\Provider as AbstractProvider;

class Provider extends AbstractProvider
{
    /**
     * 注册服务
     */
    public function register()
    {
        $this->bind('middleware', function ($server) {
            $manager = new Manager(
                $this->config('middleware', [])
            );

            $middlewares = $server->getProtocol()->getMiddlewares();

            foreach ($middlewares as $middleware) {
                $manager->registerMiddleware($middleware);
            }

            unset($middlewares);

            return $manager;
        });
    }
}
