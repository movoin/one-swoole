<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Protocols
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Protocols;

use One\Routing\Router;
use One\Context\Payload;
use One\Protocol\Protocol;
use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;
use One\Protocol\Contracts\Responder;
use One\Protocol\Responders\HttpResponder;
use One\Protocol\Exceptions\ProtocolException;
use One\Middleware\Exceptions\MiddlewareException;

class HttpProtocol extends Protocol
{
    /**
     * 响应器
     *
     * @var \One\Protocol\Contracts\Responder
     */
    protected $responder;

    /**
     * 响应 HTTP 请求
     *
     * @param  \One\Protocol\Contracts\Request  $request
     * @param  \One\Protocol\Contracts\Response $response
     */
    public function onRequest(Request $request, Response $response)
    {
        try {
            // 处理 406
            $responder = $this->getResponder();

            if ($responder->getAcceptHandler($request) === null) {
                throw ProtocolException::notAcceptable(
                    $request->getHeaderLine('Accept'),
                    $request->getProtocol()
                );
            }

            $response = $this->handle($request, $response);
        } catch (MiddlewareException $e) {
            // {{ log
            $this->logger->error(
                sprintf('中间件 %s 发生异常', $e->getMiddleware()),
                [
                    'error' => $e->getMessage(),
                    'errno' => $e->getCode()
                ]
            );
            // }}

            $response = $responder(
                $request,
                $response,
                new Payload(
                    500,
                    $e->getMessage()
                )
            );
        } catch (ProtocolException $e) {
            // {{ log
            $this->logger->error(
                sprintf('处理 %s 请求发生异常', strtoupper($e->getProtocol())),
                [
                    'error' => $e->getMessage(),
                    'errno' => $e->getCode(),
                    'uri' => $request->getRequestTarget(),
                ]
            );
            // }}

            $response = $e->makeResponse($request, $response, $responder);
        }

        return $response->end();
    }

    /**
     * 解析请求
     *
     * @param \One\Protocol\Contracts\Request $request
     *
     * @return array
     * @throws \One\Protocol\Exceptions\ProtocolException
     */
    protected function dispatch(Request $request): array
    {
        $route = $this->router->match($request);
        $status = array_shift($route);

        if (Router::FOUND === $status) {
            return $route;
        }

        if (Router::METHOD_NOT_ALLOWED === $status) {
            throw ProtocolException::methodNotAllowed(
                $request->getMethod(),
                $request->getRequestTarget(),
                $request->getProtocol()
            );
        }

        unset($status, $route);

        throw ProtocolException::notFound($request->getRequestTarget());
    }

    /**
     * 获得 Responder 对象
     *
     * @return \One\Protocol\Contracts\Responder
     */
    protected function getResponder(): Responder
    {
        if ($this->responder === null) {
            $this->responder = new HttpResponder;
        }

        return $this->responder;
    }
}
