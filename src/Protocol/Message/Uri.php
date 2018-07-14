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
use One\Support\Helpers\Assert;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * 协议
     *
     * @var string
     */
    private $scheme = '';
    /**
     * 用户信息
     *
     * @var string
     */
    private $userInfo = '';
    /**
     * 主机
     *
     * @var string
     */
    private $host = '';
    /**
     * 端口
     *
     * @var int
     */
    private $port;
    /**
     * 路径
     *
     * @var string
     */
    private $path = '';
    /**
     * 查询字符
     *
     * @var string
     */
    private $query = '';
    /**
     * 分片
     *
     * @var string
     */
    private $fragment = '';

    /**
     * 构造
     *
     * @param string $uri
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $uri = '')
    {
        if ($uri !== '') {
            if (($parts = parse_url($uri)) === false) {
                throw new InvalidArgumentException(sprintf('"%s" is invalid URI', $uri));
            }

            $this->scheme   = isset($parts['scheme'])   ? $this->filterScheme($parts['scheme']) : '';
            $this->userInfo = isset($parts['user'])     ? $parts['user'] : '';
            $this->host     = isset($parts['host'])     ? $this->filterHost($parts['host']) : '';
            $this->port     = isset($parts['port'])     ? $this->filterPort($parts['port']) : null;
            $this->path     = isset($parts['path'])     ? $this->filterPath($parts['path']) : '';
            $this->query    = isset($parts['query'])    ? $this->filterQueryAndFragment($parts['query']) : '';
            $this->fragment = isset($parts['fragment']) ? $this->filterQueryAndFragment($parts['fragment']) : '';

            if (isset($parts['pass'])) {
                $this->userInfo .= ':' . $parts['pass'];
            }
        }
    }

    /**
     * 获得 URI 的协议部分
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * 获得指定协议的 URI 实例
     *
     * @param  string $scheme
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withScheme(string $scheme): self
    {
        $scheme = $this->filterScheme($scheme);

        if ($this->scheme === $scheme) {
            return $this;
        }

        $clone = clone $this;
        $clone->scheme = $scheme;

        unset($scheme);

        return $clone;
    }

    /**
     * 获得 URI 的权限部分
     *
     * @return string 格式: [user-info@]host[:port]
     */
    public function getAuthority(): string
    {
        if ($this->host === '') {
            return '';
        }

        $authority = $this->host;

        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }

        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * 获得 URI 的用户信息部分
     *
     * @return string 格式: username[:password]
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * 获得指定用户信息的 URI 实例
     *
     * @param  string       $user
     * @param  string|null  $password
     *
     * @return self
     */
    public function withUserInfo(string $user, string $password = null): self
    {
        $info = $user;

        if (! empty($password)) {
            $info .= ':' . $password;
        }

        if ($this->userInfo === $info) {
            return $this;
        }

        $clone = clone $this;
        $clone->userInfo = $info;

        unset($info);

        return $clone;
    }

    /**
     * 获得 URI 的主机部分
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * 获得指定主机地址的 URI 实例
     *
     * @param  string $host
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withHost(string $host): self
    {
        $host = $this->filterHost($host);

        if ($this->host === $host) {
            return $this;
        }

        $clone = clone $this;
        $clone->host = $host;

        unset($host);

        return $clone;
    }

    /**
     * 获得 URI 的端口部分
     *
     * @return null|int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * 获得指定接口的 URI 实例
     *
     * @param  int  $port
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withPort(int $port): self
    {
        $port = $this->filterPort($port);

        if ($this->port === $port) {
            return $this;
        }

        $clone = clone $this;
        $clone->port = $port;

        unset($port);

        return $clone;
    }

    /**
     * 获得 URI 的路径部分
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * 获得指定路径的 URI 实例
     *
     * @param  string   $path
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withPath($path): self
    {
        $path = $this->filterPath($path);

        if ($this->path === $path) {
            return $this;
        }

        $clone = clone $this;
        $clone->path = $path;

        unset($path);

        return $clone;
    }

    /**
     * 获得 URI 的查询字符部分
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * 获得指定查询字符的 URI 实例
     *
     * @param  string   $query
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withQuery($query): self
    {
        $query = $this->filterQueryAndFragment($query);

        if ($this->query === $query) {
            return $this;
        }

        $clone = clone $this;
        $clone->query = $query;

        unset($query);

        return $clone;
    }

    /**
     * 获得 URI 的区块部分
     *
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * 获得指定分片的 URI 实例
     *
     * @param  string   $fragment
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withFragment($fragment): self
    {
        $fragment = $this->filterQueryAndFragment($fragment);

        if ($this->fragment === $fragment) {
            return $this;
        }

        $clone = clone $this;
        $clone->fragment = $fragment;

        unset($fragment);

        return $clone;
    }

    /**
     * 获得 URI 字符串
     *
     * @return string
     */
    public function __toString()
    {
        $uri = '';

        if ($this->scheme !== '') {
            $uri .= $this->scheme . ':';
        }

        $authority = $this->getAuthority();

        if ($authority !== '') {
            $uri .= '//' . $authority;
        }

        if ($this->path !== '') {
            if ($this->path[0] !== '/') {
                if ($authority !== '') {
                    $this->path = '/' . $this->path;
                }
            } elseif (isset($this->path[1]) && $this->path[1] === '/') {
                if ($authority === '') {
                    $this->path = '/' . ltrim($this->path, '/');
                }
            }

            $uri .= $this->path;
        }

        if ($this->query !== '') {
            $uri .= '?' . $this->query;
        }

        if ($this->fragment !== '') {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }

    /**
     * 获得过滤成功的协议部分
     *
     * @param  string   $scheme
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    private function filterScheme(string $scheme): string
    {
        if (! Assert::oneOf($scheme, ['http', 'https', 'tcp', 'upd', 'ws', 'wss', 'one'])) {
            throw new InvalidArgumentException(sprintf('"%s" is unsupported scheme', $scheme));
        }

        return strtolower($scheme);
    }

    /**
     * 获得过滤后的端口部分
     *
     * @param  int      $port
     *
     * @return int
     * @throws \InvalidArgumentException
     */
    private function filterPort(int $port): int
    {
        if (! Assert::range($port, 1, 0xfff)) {
            throw new InvalidArgumentException(sprintf('"%d" is invalid port', $port));
        }

        return $port;
    }

    /**
     * 获得过滤后的端口部分
     *
     * @param  string   $host
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    private function filterHost(string $host): string
    {
        if (! Assert::stringNotEmpty($host)) {
            throw new InvalidArgumentException(sprintf('"%d" is invalid hostname', $host));
        }

        return strtolower($host);
    }

    /**
     * 过滤路径部分
     *
     * @param  string   $path
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    private function filterPath($path): string
    {
        if (! is_string($path)) {
            throw new InvalidArgumentException(sprintf('"%d" is invalid path', $path));
        }

        $charUnreserved = 'a-zA-Z0-9_\-\.~';
        $charSubDelims = '!\$&\'\(\)\*\+,;=';

        return preg_replace_callback(
            '/(?:[^'.$charUnreserved.$charSubDelims.'%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $path
        );
    }

    /**
     * 过滤查询字符与区块部分
     *
     * @param  string   $string
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    private function filterQueryAndFragment($string)
    {
        if (! is_string($string)) {
            throw new InvalidArgumentException(sprintf('"%d" is invalid', $string));
        }

        $charUnreserved = 'a-zA-Z0-9_\-\.~';
        $charSubDelims = '!\$&\'\(\)\*\+,;=';

        return preg_replace_callback(
            '/(?:[^'.$charUnreserved.$charSubDelims.'%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $string
        );
    }

    /**
     * URL 编码
     *
     * @param  array $match
     *
     * @return string
     */
    private function rawurlencodeMatchZero(array $match): string
    {
        return rawurlencode($match[0]);
    }
}
