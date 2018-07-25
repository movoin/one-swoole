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

use One\Protocol\Contracts\Protocol;

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
     *
     * @return self 400
     */
    public static function badRequest(string $message): self
    {
        return new static(sprintf(
            'Bad Request: `%s`',
            $message
        ), Protocol::HTTP, 400);
    }

    /**
     * 未授权请求
     *
     * @param  string $uri
     *
     * @return self 401
     */
    public static function unauthorized(string $uri): self
    {
        return new static(sprintf(
            'Unauthorized: `%s`',
            $uri
        ), Protocol::HTTP, 401);
    }

    /**
     * 禁止访问
     *
     * @return self 403
     */
    public static function forbidden(): self
    {
        return new static('Forbidden', Protocol::HTTP, 403);
    }

    /**
     * URI 未找到
     *
     * @param  string $uri
     *
     * @return self 404
     */
    public static function notFound(string $uri): self
    {
        return new static('Not Found', Protocol::HTTP, 404);
    }

    /**
     * 请求方式不允许
     *
     * @param  string $method
     * @param  string $uri
     *
     * @return self 405
     */
    public static function methodNotAllowed(string $method, string $uri): self
    {
        return new static(sprintf(
            '`%s` not allowed `%s` method',
            $uri,
            $method
        ), Protocol::HTTP, 405);
    }

    /**
     * 请求内容类型不支持
     *
     * @param string $contentType
     *
     * @return self 406
     */
    public static function notAcceptable(string $contentType): self
    {
        return new static(sprintf(
            'Content type `%s` not acceptable',
            $contentType
        ), Protocol::HTTP, 406);
    }

    /**
     * 请求次数过多
     *
     * @return self 429
     */
    public static function tooManyRequests(): self
    {
        return new static('Too Many Requests', Protocol::HTTP, 429);
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
}
