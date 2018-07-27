<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Routing
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Routing;

use One\Swoole\Provider;

class RouterProvider extends Provider
{
    /**
     * 注册服务
     */
    public function register()
    {
        $this->bind('router', function ($server), {
            $router = new Router;
            $docs = $server->get('annotation');

            foreach ($docs as $class => $doc) {
                $router->addRoute($doc['method'], $doc['route'], $class);
            }

            unset($docs);

            return $router;
        });
    }
}
