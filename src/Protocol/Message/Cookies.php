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

class Cookies
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
        'value' => '',
        'domain' => null,
        'hostonly' => null,
        'path' => null,
        'expires' => null,
        'secure' => false,
        'httponly' => false,
        'samesite' => null
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
     * 转换为 `Set-Cookie` 的头信息
     *
     * @return array
     */
    public function toHeaders(): array
    {
        $headers = [];

        foreach ($this->responseCookies as $name => $props) {
            $headers[] = $this->toHeader($name, $props);
        }

        return $headers;
    }

    /**
     * 转换为 Cookie 字符串
     *
     * @param  string $name
     * @param  array  $properties
     *
     * @return string
     */
    protected function toHeader(string $name, array $properties): string
    {
        $cookie = urlencode($name) . '=' . urlencode($properties['value']);

        if (isset($properties['domain'])) {
            $cookie .= '; domain=' . $properties['domain'];
        }

        if (isset($properties['path'])) {
            $cookie .= '; path=' . $properties['path'];
        }

        if (isset($properties['expires'])) {
            $timestamp = is_string($properties['expires']) ?
                        strtotime($properties['expires']) :
                        (int) $properties['expires'];

            if ($timestamp !== 0) {
                $cookie .= '; expires=' . gmdate('D, d-M-Y H:i:s e', $timestamp);
            }

            unset($timestamp);
        }

        if (isset($properties['secure']) && $properties['secure']) {
            $cookie .= '; secure';
        }

        if (isset($properties['hostonly']) && $properties['hostonly']) {
            $cookie .= '; Hostonly';
        }

        if (isset($properties['httponly']) && $properties['httponly']) {
            $cookie .= '; Httponly';
        }

        if (isset($properties['samesite']) &&
            in_array(strtolower($properties['samesite']), ['lax', 'strict'], true)
        ) {
            $cookie .= '; SameSite=' . $properties['samesite'];
        }

        return $cookie;
    }
}
