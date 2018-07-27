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

use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Protocol;
use FastRoute\RouteCollector;
use FastRoute\DataGenerator\GroupCountBased as Generator;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteParser\Std as Parser;

class Router
{
    /**
     * 路由调度状态
     */
    const NOT_FOUND             = Dispatcher::NOT_FOUND;
    const FOUND                 = Dispatcher::FOUND;
    const METHOD_NOT_ALLOWED    = Dispatcher::METHOD_NOT_ALLOWED;

    /**
     * 调度器实例
     *
     * @var \FastRoute\Dispatcher
     */
    private $dispatcher;
    /**
     * 路由规则
     *
     * @var array
     */
    private $routes = [];

    /**
     * 添加路由规则
     *
     * @param string|array  $methods
     * @param string        $pattern
     * @param string        $handler
     */
    public function addRoute($methods, string $pattern, string $handler)
    {
        if (is_string($methods)) {
            $methods = (array) $methods;
        }

        $methods = array_map('strtoupper', $methods);

        $this->routes[] = [
            $methods,
            $pattern,
            $handler
        ];
    }

    /**
     * 匹配请求
     *
     * @param  \One\Protocol\Contracts\Request $request
     *
     * @return array
     */
    public function match(Request $request): array
    {
        $uri = $request->getUri()->getPath();

        // HTTP & WebSocket 请求
        if ($request->getProtocol() === Protocol::HTTP || $request->getProtocol() === Protocol::WEBSOCKET) {
            return $this->getDispatcher()->dispatch(
                $request->getMethod(),
                $uri
            );
        } else { // TCP & UDP 请求
            foreach (['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTION'] as $method) {
                $route = $this->getDispatcher()->dispatch($method, $uri);

                if ($route[0] === self::FOUND) {
                    return $route;
                }

                unset($route);
            }

            return [ self::NOT_FOUND ];
        }
    }

    /**
     * 获得调度器
     *
     * @return \FastRoute\Dispatcher
     */
    protected function getDispatcher()
    {
        if ($this->dispatcher === null) {
            $collector = new RouteCollector(
                new Parser,
                new Generator
            );

            foreach ($this->routes as $route) {
                $collector->addRoute($route[0], $route[1], $route[2]);
            }

            $this->dispatcher = new Dispatcher($collector->getData());
            $this->routes = [];

            unset($collector);
        }

        return $this->dispatcher;
    }
}
