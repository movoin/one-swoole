<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Protocol\Message\Swoole
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Protocol\Message\Swoole;

class Factory
{
    public static function newHeaders()
    {
        return [
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'accept-encoding' => 'gzip, deflate, br',
            'accept-language' => 'zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7',
            'cache-control' => 'no-cache',
            'connection' => 'keep-alive',
            'dnt' => 1,
            'host' => 'foobar.com',
            'pragma' => 'no-cache',
            'referer' => 'http://foobar.com/',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_5) Chrome/67.0.3396.99 Safari/537.36',
        ];
    }

    public static function newServer($method = 'GET')
    {
        return [
            'master_time' => 1532059493,
            'path_info' => '/path/to/file',
            'remote_addr' => '127.0.0.1',
            'remote_port' => 40326,
            'request_method' => strtoupper($method),
            'request_time' => 1532059493,
            'request_time_float' => 1532059493.6149,
            'request_uri' => '/path/to/file?q=key&filter=foo',
            'server_port' => 9501,
            'server_protocol' => 'HTTP/1.1',
            'server_software' => 'swoole-http-server',
        ];
    }

    public static function newGet()
    {
        return [
            'filter' => 'foo',
            'q' => 'key',
        ];
    }

    public static function newFormPost()
    {
        return [
            'user' => [
                'name' => 'FooBar',
                'email' => 'foo@bar.com',
                'blog' => 'http://foobar.com/',
                'location' => 'Zhongshan, Guangdong, China',
            ],
            'timestamp' => 1532059567882,
            'timestamp_secret' => 'a10031ddd7562ef845e71f0cd6129a8345349fdf6eb02b0122f51fbcbe87b0a1',
        ];
    }

    public static function newCookie()
    {
        return [
            '__Host-user_session_same_site' => 'nnUyQmJdXZ5Bvescm_AeU8by1kmwE2FAeIrN1EsZHDnLO48T',
            '_ga' => 'GA1.2.405802490.1522036759',
            '_gh_sess' => 'cHhpMWNWd3hPZmhJNGVMdWR0ckZ4M01F0eTZHN3UxL0p4OVltZGY1T1FQSkk5ckM',
            '_octo' => 'GH1.1.1580316044.1522036759',
            'dotcom_user' => 'foobar',
            'logged_in' => 'yes',
            'tz' => 'Asia/Shanghai',
            'user_session' => 'nnUyQmJdXZ5Bvescm_AeU8by1kmwE2FAeIrN1EsZHDnLO48T',
        ];
    }
}
