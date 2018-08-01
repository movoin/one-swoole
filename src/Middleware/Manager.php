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

use ReflectionException;
use One\Context\Contracts\Action;
use One\Middleware\Contracts\Filter;
use One\Middleware\Contracts\Interceptor;
use One\Middleware\Contracts\Terminator;
use One\Middleware\Contracts\Middleware;
use One\Middleware\Exceptions\MiddlewareException;
use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;
use One\Support\Helpers\Arr;
use One\Support\Helpers\Reflection;

class Manager
{
    /**
     * 匹配规则
     *
     * @var array
     */
    protected $config = [];
    /**
     * 匹配请求的中间件
     *
     * @var array
     */
    protected $matched = [];

    /**
     * 构造
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 重置匹配中间件
     */
    public function reset()
    {
        $this->matched = [];
    }

    /**
     * 获得指定请求对象的中间件管理对象
     *
     * @param \One\Protocol\Contracts\Request $request
     *
     * @throws \One\Middleware\Exceptions\MiddlewareException
     */
    public function matchRequest(Request $request)
    {
        $this->reset();

        $groups = Arr::get($this->config, 'group', []);
        $matchs = Arr::get($this->config, 'match', []);

        // 全局中间件
        if (isset($matchs['*'])) {
            $wildcards = is_string($matchs['*']) ? (array) $matchs['*'] : $matchs['*'];

            foreach ($wildcards as $alias) {
                if (isset($groups[$alias])) {
                    foreach ($groups[$alias] as $name) {
                        $this->addMatched($name);
                    }
                } else {
                    $this->addMatched($alias);
                }
            }

            unset($wildcards, $matchs['*']);
        }

        // 匹配中间件
        if (count($matchs) > 0) {
            // {{
            $uri = $request->getUri()->getPath();
            // }}
            foreach ($matchs as $pattern => $name) {
                if (preg_match("#{$pattern}#i", $uri)) {
                    $this->addMatched($name);
                }
            }

            unset($uri);
        }

        unset($groups, $matchs);
    }

    /**
     * 执行匹配的过滤器
     *
     * @param \One\Protocol\Contracts\Request  $request
     * @param \One\Protocol\Contracts\Response $response
     *
     * @return \One\Protocol\Contracts\Response|null
     */
    public function executeFilters(Request $request, Response $response)
    {
        $middlewares = $this->filterMiddleware(Filter::class);

        foreach ($middlewares as $middleware) {
            if (($returnResponse = $middleware->doFilter($request, $response)) !== null) {
                return $returnResponse;
            }
            unset($returnResponse);
        }

        unset($middlewares);
    }

    /**
     * 执行匹配的拦截器
     *
     * @param \One\Protocol\Contracts\Request  $request
     * @param \One\Protocol\Contracts\Response $response
     *
     * @return [\One\Protocol\Contracts\Request, \One\Protocol\Contracts\Response]
     */
    public function executeInterceptors(Request $request, Response $response)
    {
        $middlewares = $this->filterMiddleware(Interceptor::class);

        foreach ($middlewares as $middleware) {
            list(
                $request,
                $response
            ) = $middleware->doIntercept($request, $response);
        }

        unset($middlewares);

        return [$request, $response];
    }

    /**
     * 执行匹配的结束器
     *
     * @param \One\Protocol\Contracts\Request  $request
     * @param \One\Protocol\Contracts\Response $response
     *
     * @return \One\Protocol\Contracts\Response
     */
    public function executeTerminators(Request $request, Response $response): Response
    {
        $middlewares = $this->filterMiddleware(Terminator::class);

        foreach ($middlewares as $middleware) {
            $response = $middleware->doTerminate($request, $response);
        }

        unset($middlewares);

        return $response;
    }

    /**
     * 获得指定接口的中间件
     *
     * @param string $abstract
     *
     * @return array
     */
    protected function filterMiddleware(string $abstract): array
    {
        return array_filter($this->matched, function ($middleware) use ($abstract) {
            return $middleware instanceof $abstract;
        });
    }

    /**
     * 添加已匹配的中间件
     *
     * @param string $name
     *
     * @throws \One\Middleware\Exceptions\MiddlewareException
     */
    protected function addMatched(string $name)
    {
        if (! isset($this->matched[$name])) {
            $this->matched[$name] = $this->newMiddleware($name);
        }
    }

    /**
     * 实例化中间件
     *
     * @param string $name
     *
     * @return \One\Middleware\Contracts\Middleware
     * @throws \One\Middleware\Exceptions\MiddlewareException
     */
    protected function newMiddleware(string $name): Middleware
    {
        try {
            $middleware = Reflection::newInstance($name);
        } catch (ReflectionException $e) {
            throw new MiddlewareException('Middleware error while retrieving "%s"', $name, 0, $e);
        }

        return $middleware;
    }
}
