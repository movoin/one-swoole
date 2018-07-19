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

use One\Support\Contracts\Arrayable;

class Cookies implements Arrayable
{
    /**
     * HTTP 请求 Cookies
     *
     * @var array
     */
    protected $requestCookies = [];
    /**
     * HTTP 响应 Cookies
     *
     * @var array
     */
    protected $responseCookies = [];
    /**
     * 默认 Cookie 属性
     *
     * @var array
     */
    protected $defaults = [
        'value'     => '',
        'expires'   => 0,
        'domain'    => '',
        'path'      => '/',
        'secure'    => false,
        'httponly'  => false
    ];

    /**
     * 构造
     *
     * @param array $cookies
     */
    public function __construct(array $cookies = [])
    {
        $this->requestCookies = $cookies;
    }

    /**
     * 设置默认 Cookie 属性
     *
     * @param array $settings
     */
    public function setDefaults(array $settings)
    {
        $this->defaults = array_replace($this->defaults, $settings);
    }

    /**
     * 获得 HTTP 请求 Cookie
     *
     * @param  string $name
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return isset($this->requestCookies[$name]) ? $this->requestCookies[$name] : $default;
    }

    /**
     * 设置 HTTP 响应 Cookie
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set(string $name, $value)
    {
        if (! is_array($value)) {
            $value = ['value' => (string) $value];
        }

        $this->responseCookies[$name] = array_replace($this->defaults, $value);
    }

    /**
     * 获得 Cookies 数组
     *
     * @return array
     */
    public function toArray(): array
    {
        $cookies = [];

        foreach ($this->responseCookies as $key => $cookie) {
            $cookie['expires'] = is_string($cookie['expires']) ?
                        strtotime($cookie['expires']) :
                        (int) $cookie['expires'];

            $cookies[] = ['key' => $key] + $cookie;
        }

        return $cookies;
    }
}
