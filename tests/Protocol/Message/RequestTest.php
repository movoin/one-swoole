<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Protocol\Message
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Protocol\Message;

use One\Protocol\Factory;
use One\Protocol\Message\Headers;
use One\Protocol\Message\Request;
use One\Protocol\Message\Stream;
use One\Protocol\Message\Uri;
use One\Tests\Protocol\Message\Swoole\Request as FakeRequest;

class RequestTest extends \PHPUnit\Framework\TestCase
{
    protected $request;

    public function setUp()
    {
        $this->request = Factory::newRequest(
            FakeRequest::createGetMethodRequest()
        );
    }

    public function tearDown()
    {
        $this->request = null;
    }

    public function testCustomMethod()
    {
        $request = $this->request->withAddedHeader('X-Http-Method-Override', 'PUT');
        $this->assertEquals($request->getMethod(), 'PUT', 'withAddedHeader');

        $request = $this->request->withMethod('POST')->withParsedBody(['_METHOD' => 'PUT']);
        $this->assertEquals($request->getMethod(), 'PUT', 'withParsedBody');
    }

    public function testIsMethod()
    {
        $this->assertTrue($this->request->isMethod('GET'));
    }

    public function testIsXhr()
    {
        $this->assertFalse($this->request->isXhr());

        $request = $this->request->withAddedHeader('X-Requested-With', 'XMLHttpRequest');
        $this->assertTrue($request->isXhr());
    }

    /**
     * @dataProvider provideSomeGetMethods
     */
    public function testSomeGetMethods($method, $result)
    {
        // 操作数据
        $this->assertEquals($this->request->$method(), $result);
        // 直接返回
        $this->assertEquals($this->request->$method(), $result);
    }

    /**
     * 部分简单 GETTER
     */
    public function provideSomeGetMethods()
    {
        return [
            [ 'getProtocolVersion', '1.1' ],
            [ 'getOriginalMethod', 'GET' ],
            [ 'getMethod', 'GET' ],
            [ 'getRequestTarget', '/path/to/file?q=key&filter=foo' ],
            [
                'getQueryParams',
                [
                    'q' => 'key',
                    'filter' => 'foo',
                ]
            ],
            [
                'getUploadedFiles',
                []
            ],
            [ 'getAttributes', [] ],
            [ 'getParsedBody', null ],
            [ 'getClientIP', '127.0.0.1' ],
        ];
    }

    /**
     * @dataProvider provideSomeWithMethods
     */
    public function testSomeWithMethods($method, $attributes)
    {
        $this->assertInstanceOf(
            'One\\Protocol\\Contracts\\Request',
            call_user_func_array(
                [
                    $this->request,
                    $method
                ],
                ! is_array($attributes) ? (array) $attributes : $attributes
            )
        );
    }

    public function provideSomeWithMethods()
    {
        return [
            [ 'withProtocolVersion','2.0' ],
            [ 'withMethod',         'POST' ],
            [ 'withRequestTarget',  '/path/to/file?foo=bar' ],
            [
                'withBody',
                [
                    new Stream(fopen('php://temp', 'w+'))
                ]
            ],
            [
                'withUri',
                [
                    new Uri('', '')
                ]
            ],
            [
                'withCookieParams',
                [
                    [ 'foo' => 'bar' ]
                ]
            ],
            [
                'withQueryParams',
                [
                    [ 'foo' => 'bar' ]
                ]
            ],
            [
                'withUploadedFiles',
                [
                    [ 'foo' => 'bar' ]
                ]
            ],
            [
                'withAttributes',
                [
                    [ 'foo' => 'bar' ]
                ]
            ],
            [
                'withParsedBody',
                [
                    [ 'foo' => 'bar' ]
                ]
            ],
        ];
    }
}
