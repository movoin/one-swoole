<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Message
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Message;

use InvalidArgumentException;
use One\Protocol\Factory;
use One\Protocol\Contracts\Response as ResponseInterface;
use One\Protocol\Traits\HasMessage;
use Psr\Http\Message\StreamInterface;
use Swoole\Http\Response as SwooleResponse;

class Response implements ResponseInterface
{
    use HasMessage;

    /**
     * 状态码原因映射
     *
     * @var array
     */
    protected static $messages = [
        //Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        //Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        //Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        //Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        //Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    /**
     * 状态码
     *
     * @var int
     */
    protected $status = 200;
    /**
     * 错误原因
     *
     * @var string
     */
    protected $reasonPhrase = '';

    /**
     * Swoole 响应对象
     *
     * @var \Swoole\Http\Response
     */
    private $swResponse;

    /**
     * 构造
     *
     * @param int                                       $status
     * @param \One\Protocol\Message\Headers|null        $headers
     * @param \Psr\Http\Message\StreamInterface|null    $body
     */
    public function __construct($status = 200, Headers $headers = null, StreamInterface $body = null)
    {
        $this->status = $this->filterStatus($status);
        $this->body = $body ?? Factory::newStream();
        $this->headers = $headers ?? new Headers;
    }

    /**
     * 克隆对象
     */
    public function __clone()
    {
        $this->headers = clone $this->headers;
    }

    /**
     * 发送响应
     */
    public function end()
    {
        $this->sendHeaders();
        $this->sendSecurityHeaders();
        $this->sendContent();
    }

    /**
     * 发送响应头部报文
     */
    public function sendHeaders()
    {
        // Status
        $this->swResponse->status($this->getStatusCode());
        // Headers
        foreach ($this->getHeaders() as $name => $value) {
            $this->swResponse->header($name, implode(', ', $value));
        }
    }

    /**
     * 发送安全相关的头部报文
     */
    public function sendSecurityHeaders()
    {
        $this->swResponse->header('Content-Security-Policy', "default-src 'none'");
        $this->swResponse->header('X-Content-Type-Options', 'nosniff');
        $this->swResponse->header('X-Frame-Options', 'deny');
    }

    /**
     * 发送响应内容
     */
    public function sendContent()
    {
        $this->swResponse->end((string) $this->getBody());
    }

    /**
     * 获得 Swoole 响应对象
     *
     * @return \Swoole\Http\Response
     */
    public function getSwooleResponse(): SwooleResponse
    {
        return $this->swResponse;
    }

    /**
     * 获得指定 Swoole 响应对象的响应对象
     *
     * @param  \Swoole\Http\Response $swResponse
     *
     * @return self
     */
    public function withSwooleResponse(SwooleResponse $swResponse): self
    {
        if ($this->swResponse === $swResponse) {
            return $this;
        }

        $clone = clone $this;
        $clone->swResponse = $swResponse;

        return $clone;
    }

    /**
     * 获得响应状态码
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * 获得指定状态的响应对象
     *
     * @param  int    $code
     * @param  string $reasonPhrase
     *
     * @return \One\Protocol\Contracts\Response
     * @throws \InvalidArgumentException
     */
    public function withStatus($code, $reasonPhrase = ''): self
    {
        $code = $this->filterStatus($code);

        if ($code === $this->status) {
            return $this;
        }

        if (! is_string($reasonPhrase)) {
            throw new InvalidArgumentException('ReasonPhrase must be a string');
        }

        $clone = clone $this;
        $clone->status = $code;

        if ($reasonPhrase === '' && isset(static::$messages[$code])) {
            $reasonPhrase = static::$messages[$code];
        }

        if ($reasonPhrase === '') {
            throw new InvalidArgumentException('ReasonPhrase must be supplied for this code');
        }

        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }

    /**
     * 获得错误原因
     *
     * @return string
     */
    public function getReasonPhrase(): string
    {
        if ($this->reasonPhrase) {
            return $this->reasonPhrase;
        }

        return isset(static::$messages[$this->status]) ? static::$messages[$this->status] : null;
    }

    /**
     * 获得指定头信息的消息对象
     *
     * @param  string $name
     * @param  mixed  $value
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withHeader($name, $value): self
    {
        $this->assertHeaderName($name);

        $clone = clone $this;
        $clone->headers->set($name, $value);

        if ($clone->getStatusCode() === 200 && strtolower($name) === 'location') {
            $clone = $clone->withStatus(302);
        }

        return $clone;
    }

    /**
     * 写入响应数据
     *
     * @param  string $data
     *
     * @return self
     */
    public function write($data): self
    {
        $this->getBody()->write($data);

        return $this;
    }

    /**
     * 判断是否响应 OK
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->getStatusCode() === 200;
    }

    /**
     * 判断是否响应成功
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    /**
     * 判断是否客户端错误
     *
     * @return bool
     */
    public function isClientError(): bool
    {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
    }

    /**
     * 判断是否服务端错误
     *
     * @return bool
     */
    public function isServerError(): bool
    {
        return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
    }

    /**
     * 获得过滤后的响应状态码
     *
     * @param  int $status
     *
     * @return int
     * @throws \InvalidArgumentException
     */
    protected function filterStatus($status): int
    {
        if (! is_integer($status) || $status < 100 || $status > 599) {
            throw new InvalidArgumentException('Invalid HTTP status code');
        }

        return $status;
    }
}
