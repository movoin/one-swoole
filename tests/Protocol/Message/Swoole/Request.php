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
    protected $rawContent;

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
            'request_uri' => '/path/to/file',
            'query_string' => 'q=key&filter=foo',
            'server_protocol' => 'HTTP/1.1',
        ];
        $request->cookie = [
            'foo' => 'bar',
        ];
        $request->get = [
            'filter' => 'foo',
            'q' => 'key',
        ];
        $request->rawContent = '';

        return $request;
    }

    public static function createGetMethodRequestWithHeaders(array $headers = [])
    {
        $request = new static();

        $request->fd = 1;
        $request->header = [
            'host' => 'foobar.com',
        ] + $headers;
        $request->server = [
            'path_info' => '/path/to/file',
            'remote_addr' => '127.0.0.1',
            'request_method' => 'GET',
            'server_port' => 9501,
            'request_uri' => '/path/to/file',
            'query_string' => 'q=key&filter=foo',
            'server_protocol' => 'HTTP/1.1',
        ];
        $request->cookie = [
            'foo' => 'bar',
        ];
        $request->get = [
            'filter' => 'foo',
            'q' => 'key',
        ];
        $request->rawContent = '';

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
        $request->rawContent = 'foo=bar';

        return $request;
    }

    public static function createPostFormRequest()
    {
        $request = new static();

        $request->fd = 1;
        $request->header = [
            'host' => 'foobar.com',
            'content-type' => 'application/x-www-form-urlencoded',
            'content-length' => 7
        ];
        $request->server = [
            'path_info' => '/path/to/file',
            'remote_addr' => '127.0.0.1',
            'request_method' => 'POST',
            'request_uri' => '/path/to/file',
            'server_port' => 9501,
            'server_protocol' => 'HTTP/1.1',
        ];
        $request->rawContent = 'foo=bar';

        return $request;
    }

    public static function createPostJsonRequest()
    {
        $request = new static();

        $request->fd = 1;
        $request->header = [
            'host' => 'foobar.com',
            'content-type' => 'application/json; charset=utf-8',
            'content-length' => 25
        ];
        $request->server = [
            'path_info' => '/path/to/file',
            'remote_addr' => '127.0.0.1',
            'request_method' => 'POST',
            'request_uri' => '/path/to/file',
            'server_port' => 9501,
            'server_protocol' => 'HTTP/1.1',
        ];
        $request->rawContent = '{"foo":"bar","zar":"tar"}';

        return $request;
    }

    public static function createPostXMLRequest()
    {
        $request = new static();

        $request->fd = 1;
        $request->header = [
            'host' => 'foobar.com',
            'content-type' => 'application/xhtml+xml; charset=utf-8',
            'content-length' => 79
        ];
        $request->server = [
            'path_info' => '/path/to/file',
            'remote_addr' => '127.0.0.1',
            'request_method' => 'POST',
            'request_uri' => '/path/to/file',
            'server_port' => 9501,
            'server_protocol' => 'HTTP/1.1',
        ];
        $request->rawContent = '<?xml version="1.0" encoding="UTF-8"?><root><foo>bar</foo><zar>tar</zar></root>';

        return $request;
    }

    public static function createFileUploadRequest()
    {
        $request = new static();

        $request->fd = 1;
        $request->header = [
            'host' => 'foobar.com',
            'content-type' => 'application/x-www-form-urlencoded; charset=utf-8',
            'content-length' => 79
        ];
        $request->server = [
            'path_info' => '/path/to/file',
            'remote_addr' => '127.0.0.1',
            'request_method' => 'POST',
            'request_uri' => '/path/to/file',
            'server_port' => 9501,
            'server_protocol' => 'HTTP/1.1',
        ];
        $request->files = [
            'file' => [
                'name' => 'facepalm.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => '/tmp/swoole.upfile.n3FmFr',
                'error' => 0,
                'size' => 15476
            ]
        ];

        return $request;
    }

    public function rawContent()
    {
        return $this->rawContent;
    }
}
