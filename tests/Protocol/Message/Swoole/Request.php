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

class Request extends \Swoole\Http\Request
{
    public static function createGetMethodRequest()
    {
        $request = new static();

        $request->fd = 1;
        $request->header = [
            'host' => 'foobar.com',
        ];
        $request->server = [
            'path_info' => '/path/to/file',
            'remote_addr' => '127.0.0.1',
            'request_method' => 'GET',
            'server_port' => 9501,
            'request_uri' => '/path/to/file?q=key&filter=foo',
            'server_protocol' => 'HTTP/1.1',
        ];
        $request->cookie = [
            'foo' => 'bar',
        ];
        $request->get = [
            'filter' => 'foo',
            'q' => 'key',
        ];

        return $request;
    }

    public static function createPostMethodRequest()
    {
        $request = new static();

        $request->fd = 1;
        $request->header = [
            'host' => 'foobar.com',
        ];
        $request->server = [
            'path_info' => '/path/to/file',
            'remote_addr' => '127.0.0.1',
            'request_method' => 'POST',
            'request_uri' => '/path/to/file',
            'server_port' => 9501,
            'server_protocol' => 'HTTP/1.1',
        ];
        $request->cookie = [
            'foo' => 'bar',
        ];
        $request->post = [
            'foo' => 'bar',
        ];

        return $request;
    }

    public static function createDeleteMethodRequest()
    {
        $static->method = 'POST';
    }

    public static function createFileUploadRequest()
    {
        $static->method = 'FILE';
    }

    public function rawContent()
    {
        return '';
    }
}
