<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Traits;

use InvalidArgumentException;
use One\Protocol\Message\Headers;
use One\Support\Helpers\Assert;
use Psr\Http\Message\StreamInterface;

trait HasMessage
{
    /**
     * 有效的协议版本
     *
     * @var array
     */
    protected static $validProtocolVersions = [
        '1.0' => 1,
        '1.1' => 1,
        '2.0' => 1,
        '2'   => 1
    ];
    /**
     * 协议版本
     *
     * @var string
     */
    protected $protocolVersion = '1.1';
    /**
     * 消息头信息
     *
     * @var \One\Protocol\Contracts\Headers
     */
    protected $headers;
    /**
     * 消息内容对象
     *
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $body;

    /**
     * 获得消息协议版本
     *
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * 获得指定协议版本的消息实例
     *
     * @param  string $version
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withProtocolVersion($version): self
    {
        if (! isset(self::$validProtocolVersions[$version])) {
            throw new InvalidArgumentException(
                'Invalid HTTP version. Must be one of: '
                . implode(', ', array_keys(self::$validProtocolVersions))
            );
        }

        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    /**
     * 获得所有消息头信息
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers->all();
    }

    /**
     * 判断是否存在指定头信息
     *
     * @param  string $name
     *
     * @return bool
     */
    public function hasHeader($name): bool
    {
        return $this->headers->has($name);
    }

    /**
     * 获得指定头信息
     *
     * @param  string $name
     *
     * @return array
     */
    public function getHeader($name): array
    {
        return $this->headers->get($name, []);
    }

    /**
     * 获得指定头信息字符串（以 , 分隔）
     *
     * @param  string $name
     *
     * @return string
     */
    public function getHeaderLine($name): string
    {
        return implode(',', $this->headers->get($name, []));
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

        return $clone;
    }

    /**
     * 获得添加具体头信息的消息对象
     *
     * @param  string $name
     * @param  mixed  $value
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withAddedHeader($name, $value): self
    {
        $this->assertHeaderName($name);

        $clone = clone $this;
        $clone->headers->add($name, $value);

        return $clone;
    }

    /**
     * 获得移除具体头信息的消息对象
     *
     * @param  string $name
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withoutHeader($name): self
    {
        $this->assertHeaderName($name);

        $clone = clone $this;
        $clone->header->remove($name);

        return $clone;
    }

    /**
     * 获得消息正文
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getBody(): StreamInterface
    {
        if ($this->body->eof()) {
            $this->body->rewind();
        }

        return $this->body;
    }

    /**
     * 获得具体消息正义的消息对象
     *
     * @param  \Psr\Http\Message\StreamInterface $body
     *
     * @return self
     */
    public function withBody(StreamInterface $body): self
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    /**
     * 判定合法的头信息键名
     *
     * @param  string $name
     *
     * @throws \InvalidArgumentException
     */
    private function assertHeaderName($name)
    {
        if (! Assert::stringNotEmpty($name)) {
            throw new InvalidArgumentException('Invalid header name');
        }
    }
}
