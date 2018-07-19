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

use One\Protocol\Contracts\Protocol;
use One\Protocol\Contracts\Request as RequestInterface;
use One\Protocol\Contracts\Response as ResponseInterface;
use One\Protocol\Exceptions\ProtocolException;
use One\Protocol\Message\Cookies;
use One\Protocol\Message\Headers;
use One\Protocol\Message\Request;
use One\Protocol\Message\Response;
use One\Protocol\Message\Stream;
use One\Protocol\Message\UploadedFile;
use One\Protocol\Message\Uri;
use One\Support\Helpers\Arr;
use One\Support\Helpers\Reflection;
use Psr\Http\Message\StreamInterface;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

final class Factory
{
    /**
     * 支持协议
     *
     * @var array
     */
    private static $protocols = [
        Protocol::HTTP      => 'HttpProtocol',
        Protocol::TCP       => 'TcpProtocol',
        Protocol::UDP       => 'UdpProtocol',
        Protocol::WEBSOCKET => 'WebSocketProtocol',
    ];

    /**
     * 创建协议
     *
     * @param  string $protocol
     *
     * @return \One\Protocol\Contracts\Protocol
     * @throws \One\Protocol\Exceptions\ProtocolException
     */
    public static function newProtocol(string $protocol): Protocol
    {
        if (! in_array($protocol, static::$protocols)) {
            throw ProtocolException::notSupport($ptotocol);
        }

        return Reflection::newInstance(
            '\\One\\Protocol\\Protocols\\' . static::$protocols[$ptotocol]
        );
    }

    /**
     * 创建请求对象
     *
     * @param  Swoole\Http\Request $swoole
     *
     * @return \One\Protocol\Contracts\Request
     */
    public static function newRequest(SwooleRequest $swoole): RequestInterface
    {
        // {{ Headers
        $headers = new Headers;
        $swHeaders = $swoole->header ?? [];

        foreach ($swHeaders as $key => $value) {
            $headers->add(
                implode('-', array_map('ucfirst', explode('-', $key))),
                $value
            );
        }
        // }}

        // {{ Server Parameters
        $swServer = $swoole->server ?? [];
        $server = $swServer ? array_change_key_case($swServer, CASE_UPPER) : [];

        foreach ($swHeaders as $key => $value) {
            $server[sprintf('HTTP_%s', strtoupper(str_replace('-', '_', $key)))] = $value;
        }

        unset($swHeaders, $swServer);
        // }}

        // {{ Request Method
        $method = Arr::get($server, 'REQUEST_METHOD', 'GET');
        // }}

        // {{ Request Uri
        $uri = new Uri(
            '',
            Arr::get($server, 'HTTP_HOST', ''),
            Arr::get($server, 'SERVER_PORT', ''),
            Arr::get($server, 'REQUEST_URI', '/'),
            Arr::get($server, 'QUERY_STRING', '')
        );
        // }}

        // {{ Request Cookies
        $cookies = new Cookies($swoole->cookie ?? []);
        // }}

        // {{ Uploaded Files
        $uploadedFiles = array_map(function ($file) {
            return new UploadedFile(
                $file['tmp_name'],
                $file['name'],
                $file['type'],
                $file['size'],
                $file['error']
            );
        }, $swoole->files ?? []);
        // }}

        $request = new Request(
            $method,
            $uri,
            $headers,
            $cookies,
            $server,
            static::newStream($swoole->rawContent()),
            $uploadedFiles
        );

        if ($method === 'POST' && $swoole->post) {
            $request = $request->withParsedBody($swoole->post);
        }

        unset($method, $uri, $headers, $cookies, $server, $uploadedFiles);

        return $request;
    }

    /**
     * 创建响应对象
     *
     * @param  Swoole\Http\Response $swoole
     *
     * @return \One\Protocol\Contracts\Response
     */
    public static function newResponse(SwooleResponse $swoole): ResponseInterface
    {
    }

    /**
     * 创建文件流对象
     *
     * @param  \Psr\Http\Message\StreamInterface|resource|string|null $body
     *
     * @return \Psr\Http\Message\StreamInterface
     * @throws \InvalidArgumentException
     */
    public static function newStream($body = null): StreamInterface
    {
        if ($body instanceof StreamInterface) {
            return $body;
        }

        if (is_resource($body)) {
            return new Stream($body);
        }

        $stream = new Stream(fopen('php://temp', 'w+'));
        ! empty($body) && $stream->write($body);

        return $stream;
    }
}
