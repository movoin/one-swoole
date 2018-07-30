<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol;

use ReflectionException;
use One\Protocol\Contracts\Protocol as ProtocolInterface;
use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;
use One\Protocol\Contracts\Responder;
use One\Protocol\Exceptions\ProtocolException;
use One\Protocol\Traits\HasServer;
use One\Protocol\Traits\HasServerContainer;
use One\Protocol\Traits\HasStartItem;
use One\Support\Helpers\Reflection;

abstract class Protocol implements ProtocolInterface
{
    use HasServer,
        HasServerContainer,
        HasStartItem;

    /**
     * 协议处理
     *
     * @param  \One\Protocol\Contracts\Request   $request
     * @param  \One\Protocol\Contracts\Response  $response
     *
     * @return \One\Protocol\Contracts\Response
     * @throws \One\Protocol\Exceptions\ProtocolException
     * @throws \One\Middleware\Exceptions\MiddlewareException
     */
    public function handle(Request $request, Response $response): Response
    {
        // 解析请求
        list($action, $args) = $this->dispatch($request);

        // 写入请求参数至请求对象
        $request = $request->withAttributes($args);

        // 匹配请求对应中间件
        $this->middleware->matchRequest($request);

        // {{ 执行请求过滤中间件
        if (($return = $this->middleware->executeFilters($request, $response)) !== null) {
            return $return;
        }
        unset($return);
        // }}

        // {{ 执行请求拦截中间件
        list(
            $request,
            $response
        ) = $this->middleware->executeInterceptors($request, $response);
        // }}

        // {{ 执行请求动作
        try {
            $action = Reflection::newInstance($action, [$this->getServer()]);
        } catch (ReflectionException $e) {
            throw ProtocolException::notFound(
                $request->getRequestTarget(),
                $request->getProtocol()
            );
        }

        $payload = $action($request);
        $responder = $this->getResponder();
        $response = $responder($request, $response, $payload);
        unset($action, $payload);
        // }}

        // {{ 执行请求结束中间件
        $response = $this->middleware->executeTerminators($request, $response);
        // }}

        // {{ log
        $this->logger->info('响应结果', [
            'type' => strtoupper($request->getProtocol()),
            'uri' => $request->getRequestTarget(),
            'result' => (string) $response->getBody()
        ]);
        // }}

        unset($request, $responder);

        return $response;
    }

    /**
     * 解析请求
     *
     * @param \One\Protocol\Contracts\Request $request
     *
     * @return array
     */
    abstract protected function dispatch(Request $request): array;

    /**
     * 获得 Responder 对象
     *
     * @return \One\Protocol\Contracts\Responder
     */
    abstract protected function getResponder(): Responder;
}
