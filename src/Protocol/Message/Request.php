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
use One\Protocol\Contracts\Request as RequestInterface;
use One\Protocol\Traits\HasMessage;
use One\Protocol\Traits\HasProtocol;
use One\Protocol\Message\Cookies;
use One\Support\Collection;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class Request implements RequestInterface
{
    use HasMessage,
        HasProtocol;

    /**
     * 请求方法
     *
     * @var string
     */
    protected $method;
    /**
     * 原请求方法
     *
     * @var string
     */
    protected $originalMethod;
    /**
     * 请求 URI 对象
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $uri;
    /**
     * 请求 URI 目标 (Path + QueryString)
     *
     * @var string
     */
    protected $requestTarget;
    /**
     * 查询参数
     *
     * @var array
     */
    protected $queryParams;
    /**
     * 请求 Cookies
     *
     * @var \One\Protocol\Message\Cookies
     */
    protected $cookies;
    /**
     * 服务器环境变量
     *
     * @var array
     */
    protected $serverParams;
    /**
     * 请求参数
     *
     * @var array
     */
    protected $attributes;
    /**
     * 标识请求内容是否已经解析
     *
     * @var null|array|object
     */
    protected $parsedBody;
    /**
     * 上传文件列表
     *
     * @var array
     */
    protected $uploadedFiles;

    /**
     * 构造
     *
     * @param string                            $method
     * @param \Psr\Http\Message\UriInterface    $uri
     * @param Headers                           $headers
     * @param \One\Protocol\Message\Cookies     $cookies
     * @param array                             $serverParams
     * @param \Psr\Http\Message\StreamInterface $body
     * @param array                             $uploadedFiles
     *
     * @throws \InvalidArgumentException
     * @throws \InvalidMethodException
     */
    public function __construct(
        $method,
        UriInterface $uri,
        Headers $headers,
        Cookies $cookies,
        array $serverParams,
        StreamInterface $body,
        array $uploadedFiles = []
    ) {
        $this->originalMethod = $this->filterMethod($method);
        $this->uri = $uri;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->serverParams = $serverParams;
        $this->attributes = new Collection;
        $this->body = $body;
        $this->uploadedFiles = $uploadedFiles;

        if (isset($serverParams['SERVER_PROTOCOL'])) {
            $this->protocolVersion = str_replace('HTTP/', '', $serverParams['SERVER_PROTOCOL']);
        }

        if (! $this->headers->has('Host') && $this->uri->getHost() !== '') {
            $port = $this->uri->getPort() ? ":{$this->uri->getPort()}" : '';
            $this->headers->set('Host', $this->uri->getHost() . $port);
            unset($port);
        }
    }

    /**
     * 克隆对象
     */
    public function __clone()
    {
        $this->headers = clone $this->headers;
        $this->attributes = clone $this->attributes;
        $this->body = clone $this->body;
    }

    /**
     * 获得原请求方法
     *
     * @return string
     */
    public function getOriginalMethod(): string
    {
        return $this->originalMethod;
    }

    /**
     * 获得请求方法
     *
     * @return string
     * @throws \InvalidArgumentException
     * @throws \InvalidMethodException
     */
    public function getMethod(): string
    {
        if ($this->method === null) {
            $this->method = $this->originalMethod;
            $customMethod = $this->getHeaderLine('X-Http-Method-Override');

            if ($customMethod) {
                $this->method = $this->filterMethod($customMethod);
            } elseif ($this->originalMethod === 'POST') {
                if (($overrideMethod = $this->filterMethod($this->getParsedBodyParam('_METHOD'))) !== null) {
                    $this->method = $overrideMethod;
                }

                if ($this->getBody()->eof()) {
                    $this->getBody()->rewind();
                }
            }
        }

        return $this->method;
    }

    /**
     * 获得指定请求方法的请求对象
     *
     * @param  string $method
     *
     * @return \One\Protocol\Contracts\Request
     * @throws \InvalidArgumentException
     * @throws \InvalidMethodException
     */
    public function withMethod($method): RequestInterface
    {
        $method = $this->filterMethod($method);

        $clone = clone $this;
        $clone->originalMethod = $method;
        $clone->method = $method;

        return $clone;
    }

    /**
     * 判断是否指定请求方法
     *
     * @param  string $method
     *
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \InvalidMethodException
     */
    public function isMethod(string $method): bool
    {
        return $this->getMethod() === $method;
    }

    /**
     * 判断是否 XHR 请求
     *
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \InvalidMethodException
     */
    public function isXhr(): bool
    {
        return $this->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * 获得请求目标
     *
     * @return string
     */
    public function getRequestTarget(): string
    {
        if ($this->requestTarget) {
            return $this->requestTarget;
        }

        if ($this->uri === null) {
            return '/';
        }

        $path = '/' . ltrim($this->uri->getPath(), '/');

        if ($query = $this->uri->getQuery()) {
            $path .= '?' . $query;
        }

        return $this->requestTarget = $path;
    }

    /**
     * 获得指定请求目标的请求对象
     *
     * @param  mixed $requestTarget
     *
     * @return \One\Protocol\Contracts\Request
     * @throws \InvalidArgumentException
     */
    public function withRequestTarget($requestTarget): RequestInterface
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException(
                'Invalid request target provided; must be a string and cannot contain whitespace'
            );
        }

        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    /**
     * 获得请求 Uri 对象
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * 获得指定请求 Uri 对象的请求对象
     *
     * @param  \Psr\Http\Message\UriInterface   $uri
     * @param  bool                             $preserveHost
     *
     * @return \One\Protocol\Contracts\Request
     */
    public function withUri(UriInterface $uri, $preserveHost = false): RequestInterface
    {
        $clone = clone $this;
        $clone->uri = $uri;

        if (! $preserveHost) {
            if ($uri->getHost() !== '') {
                $clone->headers->set('Host', $uri->getHost());
            }
        } else {
            if ($uri->getHost() !== '' && (! $this->hasHeader('Host') || $this->getHeaderLine('Host') === '')) {
                $clone->headers->set('Host', $uri->getHost());
            }
        }

        return $clone;
    }

    /**
     * 获得 Cookies
     *
     * @return array
     */
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    /**
     * 获得指定键名的 Cookie
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function getCookieParam(string $key, $default = null)
    {
        $cookies = $this->getCookieParams();

        return isset($cookies[$key]) ? $cookies[$key] : $default;
    }

    /**
     * 获得指定 Cookies 的请求对象
     *
     * @param  array  $cookies
     *
     * @return \One\Protocol\Contracts\Request
     */
    public function withCookieParams(array $cookies): RequestInterface
    {
        $clone = clone $this;
        $clone->cookies = $cookies;

        return $clone;
    }

    /**
     * 获得查询参数
     *
     * @return array
     */
    public function getQueryParams(): array
    {
        if (is_array($this->queryParams)) {
            return $this->queryParams;
        }

        if ($this->uri === null) {
            return [];
        }

        parse_str($this->uri->getQuery(), $this->queryParams);

        return $this->queryParams;
    }

    /**
     * 获得指定查询参数的请求对象
     *
     * @param  array  $query
     *
     * @return \One\Protocol\Contracts\Request
     */
    public function withQueryParams(array $query): RequestInterface
    {
        $clone = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    /**
     * 获得上传文件
     *
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * 获得指定上传文件的请求对象
     *
     * @param  array  $uploadedFiles
     *
     * @return \One\Protocol\Contracts\Request
     */
    public function withUploadedFiles(array $uploadedFiles): RequestInterface
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    /**
     * 获得服务器环境
     *
     * @return array
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * 获得指定服务环境
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function getServerParam(string $key, $default = null)
    {
        $serverParams = $this->getServerParams();

        return isset($serverParams[$key]) ? $serverParams[$key] : $default;
    }

    /**
     * 获得请求参数
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes->all();
    }

    /**
     * 获得指定请求参数
     *
     * @param  string $name
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return $this->attributes($name, $default);
    }

    /**
     * 获得指定请求参数的请求对象
     *
     * @param  string $name
     * @param  mixed  $value
     *
     * @return \One\Protocol\Contracts\Request
     */
    public function withAttribute($name, $value): RequestInterface
    {
        $clone = clone $this;
        $clone->attributes->set($name, $value);

        return $clone;
    }

    /**
     * 获得指定请求参数数组的请求对象
     *
     * @param  array  $attributes
     *
     * @return \One\Protocol\Contracts\Request
     */
    public function withAttributes(array $attributes): RequestInterface
    {
        $clone = clone $this;
        $clone->attributes = new Collection($attributes);

        return $clone;
    }

    /**
     * 获得排除指定请求参数的请求对象
     *
     * @param  string $name
     *
     * @return \One\Protocol\Contracts\Request
     */
    public function withoutAttribute($name): RequestInterface
    {
        $clone = clone $this;
        $clone->attributes->remove($name);

        return $clone;
    }

    /**
     * 获得解析后的消息内容
     *
     * @return null|array|object
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * 获得指定内容的请求对象
     *
     * @param  null|array|object $data
     *
     * @return \One\Protocol\Contracts\Request
     * @throws \InvalidArgumentException
     */
    public function withParsedBody($data): RequestInterface
    {
        if (! is_null($data) && ! is_object($data) && ! is_array($data)) {
            throw new InvalidArgumentException('Parsed body value must be an array, an object, or null');
        }

        $clone = clone $this;
        $clone->parsedBody = $data;

        return $clone;
    }

    /**
     * 获得请求参数 (GET, POST)
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function getParam(string $key, $default = null)
    {
        $postParams = $this->getParsedBody();
        $getParams = $this->getQueryParams();
        $result = $default;

        if (is_array($postParams) && isset($postParams[$key])) {
            $result = $postParams[$key];
        } elseif (is_object($postParams) && property_exists($postParams, $key)) {
            $result = $postParams->$key;
        } elseif (isset($getParams[$key])) {
            $result = $getParams[$key];
        }

        unset($postParams, $getParams);

        return $result;
    }

    /**
     * 获得 POST 请求参数
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function getParsedBodyParam(string $key, $default = null)
    {
        $postParams = $this->getParsedBody();
        $result = $default;

        if (is_array($postParams) && isset($postParams[$key])) {
            $result = $postParams[$key];
        } elseif (is_object($postParams) && property_exists($postParams, $key)) {
            $result = $postParams->$key;
        }

        unset($postParams);

        return $result;
    }

    /**
     * 获得 GET 请求参数
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function getQueryParam(string $key, $default = null)
    {
        $getParams = $this->getQueryParams();
        $result = $default;

        if (isset($getParams[$key])) {
            $result = $getParams[$key];
        }

        unset($getParams);

        return $result;
    }

    /**
     * 获得请求 IP 地址
     *
     * @return string
     */
    public function getClientIP(): string
    {
        $ip = $this->getServerParam('REMOTE_ADDR');

        if ($this->getServerParam('HTTP_X_FORWARDED_FOR') !== null) {
            $ip = $this->getServerParam('HTTP_X_FORWARDED_FOR');
        }

        if ($this->getServerParam('HTTP_CLIENT_IP') !== null) {
            $ip = $this->getServerParam('HTTP_CLIENT_IP');
        }

        if ($this->headers->has('X-Real-Ip')) {
            $ip = $this->headers->getHeaderLine('X-Real-Ip');
        }

        if ($this->headers->has('X-Client-Ip')) {
            $ip = $this->headers->getHeaderLine('X-Client-Ip');
        }

        return is_null($ip) ? 'Unknow' : $ip;
    }

    /**
     * 过滤请求方法
     *
     * @param  null|string $method
     *
     * @return null|string
     * @throws \InvalidArgumentException
     * @throws \InvalidMethodException
     */
    protected function filterMethod($method)
    {
        if ($method === null) {
            return $method;
        }

        if (! is_string($method)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unsupported HTTP method; must be a string, received %s',
                    (is_object($method) ? get_class($method) : gettype($method))
                )
            );
        }

        $method = strtoupper($method);

        if (preg_match("/^[!#$%&'*+.^_`|~0-9a-z-]+$/i", $method) !== 1) {
            throw new InvalidMethodException($this, $method);
        }

        return $method;
    }
}
