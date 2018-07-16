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
use One\Protocol\Exceptions\ProtocolException;
use One\Protocol\Message\Stream;
use One\Protocol\Message\UploadedFile;
use One\Support\Helpers\Assert;
use One\Support\Helpers\Reflection;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

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
        if (! Assert::oneOf($protocol, static::$protocols)) {
            throw ProtocolException::notSupport($ptotocol);
        }

        return Reflection::newInstance(
            '\\One\\Protocol\\Protocols\\' . static::$protocols[$ptotocol]
        );
    }

    /**
     * 创建文件流对象
     *
     * @param  \Psr\Http\Message\StreamInterface|string|resource|null $body
     *
     * @return \Psr\Http\Message\StreamInterface
     * @throws \InvalidArgumentException
     */
    public static function newStream($body = null): StreamInterface
    {
        if (Assert::instanceOf($body, StreamInterface::class)) {
            return $body;
        }

        return new Stream($body === null ? '' : $body);
    }

    /**
     * 创建上传文件对象
     *
     * @param  array  $file
     *
     * @return \Psr\Http\Message\UploadedFileInterface
     * @throws \InvalidArgumentException
     */
    public static function newUploadedFile(array $file): UploadedFileInterface
    {
        return new UploadedFile(
            $file['tmp_name'],
            $file['size'],
            $file['error'],
            $file['name'],
            $file['type']
        );
    }
}
