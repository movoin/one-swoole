<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Exceptions
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Exceptions;

use One\Context\Payload;
use One\Protocol\Contracts\Protocol;
use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;
use One\Protocol\Contracts\Responder;

class ProtocolException extends \RuntimeException
{
    /**
     * 协议
     *
     * @var string
     */
    protected $protocol;

    /**
     * 协议不支持
     *
     * @param  string $protocol
     *
     * @return self
     */
    public static function notSupport(string $protocol): self
    {
        return new static('Protocol "%s" does not support', $protocol);
    }

    /**
     * 非法请求
     *
     * @param  string $message
     * @param  string $protocol
     *
     * @return self 400
     */
    public static function badRequest(string $message, string $protocol = Protocol::HTTP): self
    {
        return new static(sprintf(
            'Bad Request: `%s`',
            $message
        ), $protocol, 400);
    }

    /**
     * 未授权请求
     *
     * @param  string $uri
     * @param  string $protocol
     *
     * @return self 401
     */
    public static function unauthorized(string $uri, string $protocol = Protocol::HTTP): self
    {
        return new static(sprintf(
            'Unauthorized: `%s`',
            $uri
        ), $protocol, 401);
    }

    /**
     * 禁止访问
     *
     * @param  string $protocol
     *
     * @return self 403
     */
    public static function forbidden(string $protocol = Protocol::HTTP): self
    {
        return new static('Forbidden', $protocol, 403);
    }

    /**
     * URI 未找到
     *
     * @param  string $uri
     * @param  string $protocol
     *
     * @return self 404
     */
    public static function notFound(string $uri, string $protocol = Protocol::HTTP): self
    {
        return new static('Not Found', $protocol, 404);
    }

    /**
     * 请求方式不允许
     *
     * @param  string $method
     * @param  string $uri
     * @param  string $protocol
     *
     * @return self 405
     */
    public static function methodNotAllowed(string $method, string $uri, string $protocol = Protocol::HTTP): self
    {
        return new static(sprintf(
            '`%s` not allowed `%s` method',
            $uri,
            $method
        ), $protocol, 405);
    }

    /**
     * 请求内容类型不支持
     *
     * @param  string $contentType
     * @param  string $protocol
     *
     * @return self 406
     */
    public static function notAcceptable(string $contentType, string $protocol = Protocol::HTTP): self
    {
        return new static(sprintf(
            'Content type `%s` not acceptable',
            $contentType
        ), $protocol, 406);
    }

    /**
     * 请求次数过多
     *
     * @param  string $protocol
     *
     * @return self 429
     */
    public static function tooManyRequests(string $protocol = Protocol::HTTP): self
    {
        return new static('Too Many Requests', $protocol, 429);
    }

    /**
     * 构造异常
     *
     * @param string          $message
     * @param string          $protocol
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct(string $message, string $protocol, int $code = 0, \Exception $previous = null)
    {
        $this->setProtocol($protocol);
        parent::__construct(sprintf($message, $protocol), $code, $previous);
    }

    /**
     * 获得协议
     *
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * 设置协议
     *
     * @param  string $protocol
     */
    public function setProtocol(string $protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * 获得异常响应对象
     *
     * @param  \One\Protocol\Contracts\Request      $request
     * @param  \One\Protocol\Contracts\Response     $response
     * @param  \One\Protocol\Contracts\Responder    $responder
     *
     * @return \One\Protocol\Contracts\Response
     */
    public function makeResponse(Request $request, Response $response, Responder $responder): Response
    {
        return $responder(
            $request,
            $response,
            new Payload(
                $this->getCode(),
                $this->getMessage()
            )
        );
    }
}
