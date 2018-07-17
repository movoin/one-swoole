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
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /**
     * Uri 协议部分 (不含 '://')
     *
     * @var string
     */
    protected $scheme = '';
    /**
     * Uri 用户名部分
     *
     * @var string
     */
    protected $user = '';
    /**
     * Uri 密码部分
     *
     * @var string
     */
    protected $password = '';
    /**
     * Uri 主机地址部分
     *
     * @var string
     */
    protected $host = '';
    /**
     * Uri 主机端口部分
     *
     * @var null|int
     */
    protected $port;
    /**
     * Uri 路径部分
     *
     * @var string
     */
    protected $path = '';
    /**
     * Uri 查询参数部分 (不含 '?')
     *
     * @var string
     */
    protected $query = '';
    /**
     * Uri 分片部分 (不含 '#')
     *
     * @var string
     */
    protected $fragment = '';

    /**
     * 构造
     *
     * @param string $scheme
     * @param string $host
     * @param int    $port
     * @param string $path
     * @param string $query
     * @param string $fragment
     * @param string $user
     * @param string $password
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $scheme,
        $host,
        $port = null,
        $path = '/',
        $query = '',
        $fragment = '',
        $user = '',
        $password = ''
    ) {
        $this->scheme = $this->filterScheme($scheme);
        $this->host = $host;
        $this->port = $this->filterPort($port);
        $this->path = empty($path) ? '/' : $this->filterPath($path);
        $this->query = $this->filterQuery($query);
        $this->fragment = $this->filterQuery($fragment);
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * 获得 Uri 协议部分
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * 获得指定 Uri 协议的 Uri 对象
     *
     * @param  string $scheme
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withScheme($scheme): UriInterface
    {
        $scheme = $this->filterScheme($scheme);

        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    /**
     * 获得 Uri 的权限部分
     *
     * @return string
     */
    public function getAuthority(): string
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();

        return ($userInfo ? $userInfo . '@' : '') . $host . ($port !== null ? ':' . $port : '');
    }

    /**
     * 获得用户信息
     *
     * @return string
     */
    public function getUserInfo()
    {
        return $this->user . ($this->password ? ':' . $this->password : '');
    }

    /**
     * 获得指定用户信息的 Uri 对象
     *
     * @param  string       $user
     * @param  null|string  $password
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withUserInfo($user, $password = null): UriInterface
    {
        if (! is_string($user)) {
            throw new InvalidArgumentException('Uri user must be a string');
        }

        $clone = clone $this;
        $clone->user = $this->filterUserInfo($user);

        if ($clone->user) {
            $clone->password = $password ? $this->filterUserInfo($password) : '';
        } else {
            $clone->password = '';
        }

        return $clone;
    }

    /**
     * 获得 Uri 主机部分
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * 获得指定主机地址的 Uri 对象
     *
     * @param  string $host
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withHost($host): UriInterface
    {
        if (! is_string($host)) {
            throw new InvalidArgumentException('Uri host must be a string');
        }

        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    /**
     * 获得 Uri 端口部分
     *
     * @return null|int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * 获得指定端口的 Uri 对象
     *
     * @param  null|int $port
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withPort($port): UriInterface
    {
        $port = $this->filterPort($port);

        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    /**
     * 获得 Uri 路径部分
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * 获得指定路径的 Uri 对象
     *
     * @param  string $path
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withPath($path): UriInterface
    {
        if (! is_string($path)) {
            throw new InvalidArgumentException('Uri path must be a string');
        }

        $clone = clone $this;
        $clone->path = $this->filterPath($path);

        return $clone;
    }

    /**
     * 获得 Uri 查询参数
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * 获得指定查询参数的 Uri 对象
     *
     * @param  string $query
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withQuery($query): UriInterface
    {
        if (! is_string($query)) {
            throw new InvalidArgumentException('Uri query must be a string');
        }

        $query = ltrim((string) $query, '?');

        $clone = clone $this;
        $clone->query = $this->filterQuery($query);

        return $clone;
    }

    /**
     * 获得 Uri 分片部分
     *
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * 获得指定分片的 Uri 对象
     *
     * @param  string $fragment
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function withFragment($fragment): UriInterface
    {
        if (! is_string($fragment)) {
            throw new InvalidArgumentException('Uri fragment must be a string');
        }

        $fragment = ltrim((string) $fragment, '#');

        $clone = clone $this;
        $clone->fragment = $this->filterQuery($fragment);

        return $clone;
    }

    /**
     * 过滤 Uri 协议部分
     *
     * @param  string $scheme
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function filterScheme($scheme): string
    {
        static $valid = [
            ''      => 1,
            'https' => 1,
            'http'  => 1,
        ];

        if (! is_string($scheme)) {
            throw new InvalidArgumentException('Uri scheme must be a string');
        }

        $scheme = str_replace('://', '', strtolower((string) $scheme));

        if (! isset($valid[$scheme])) {
            throw new InvalidArgumentException('Uri scheme must be one of: "", "https", "http"');
        }

        return $scheme;
    }

    /**
     * 获得 Uri 字符串
     *
     * @return string
     */
    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        return ($scheme ? $scheme . ':' : '')
            . ($authority ? '//' . $authority : '')
            . $path
            . ($query ? '?' . $query : '')
            . ($fragment ? '#' . $fragment : '');
    }

    /**
     * 过滤 Uri 用户信息部分
     *
     * @param  string $query
     *
     * @return string
     */
    protected function filterUserInfo(string $query): string
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=]+|%(?![A-Fa-f0-9]{2}))/u',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $query
        );
    }

    /**
     * 过滤端口
     *
     * @param  null|int $port
     *
     * @return null|int
     * @throws \InvalidArgumentException
     */
    protected function filterPort($port)
    {
        if (is_null($port) || (is_integer($port) && ($port >= 1 && $port <= 65535))) {
            return $port;
        }

        throw new InvalidArgumentException('Uri port must be null or an integer between 1 and 65535 (inclusive)');
    }

    /**
     * 过滤 Uri 路径
     *
     * @param  string $path
     *
     * @return string
     */
    protected function filterPath($path): string
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }

    /**
     * 过滤查询条件或分片
     *
     * @param  string $query
     *
     * @return string
     */
    protected function filterQuery($query): string
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $query
        );
    }
}
