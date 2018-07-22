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

    #### GET Request ####

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

    public function testGetUri()
    {
       $this->assertInstanceOf('Psr\\Http\\Message\\UriInterface', $this->request->getUri());
    }

    public function testGetCookieParams()
    {
       $this->assertInstanceOf('One\\Protocol\\Message\\Cookies', $this->request->getCookieParams());
    }

    public function testGetCookieParam()
    {
        $this->assertEquals('bar', $this->request->getCookieParam('foo'));
    }

    public function testGetAttribute()
    {
        $request = $this->request->withAttribute('foo', 'bar');

        $this->assertEquals('bar', $request->getAttribute('foo'));
        $this->assertEquals('bar', $request->attribute('foo'));
    }

    public function testWithoutAttribute()
    {
        $request = $this->request->withAttribute('foo', 'bar');
        $request = $this->request->withoutAttribute('foo');

        $this->assertNull($request->getAttribute('foo'));
    }

    public function testGetQueryParam()
    {
        $this->assertEquals('key', $this->request->get('q'));
        $this->assertEquals('key', $this->request->param('q'));
    }

    public function testGetHeader()
    {
        $this->assertEquals('foobar.com', $this->request->header('Host'));
        $this->assertNull($this->request->header('bad'));
    }

    public function testWithHeader()
    {
        $request = $this->request->withHeader('abc', 'xyz');
        $this->assertEquals($request->getHeaderLine('abc'), 'xyz');
    }

    public function testWithoutHeader()
    {
        $request = $this->request->withHeader('foo', 'bar');
        $request = $this->request->withoutHeader('foo');

        $this->assertEquals([], $request->getHeader('foo'));
    }

    public function testGetHeaders()
    {
        $headers = $this->request->getHeaders();
        $this->assertEquals(['foobar.com'], $headers['Host']);
    }

    public function testGetServer()
    {
        $this->assertEquals('/path/to/file', $this->request->server('PATH_INFO'));
    }

    public function testGetCookie()
    {
        $this->assertEquals('bar', $this->request->cookie('foo'));
    }

    public function testClientIP()
    {
        $request = $this->request->withAddedHeader('X-Real-Ip', '192.168.0.1');
        $this->assertEquals('192.168.0.1', $request->getClientIP(), 'X-Real-Ip');

        $request = $this->request->withAddedHeader('X-Client-Ip', '192.168.0.1');
        $this->assertEquals('192.168.0.1', $request->getClientIP(), 'X-Client-Ip');

        $request = Factory::newRequest(
            FakeRequest::createGetMethodRequestWithHeaders([
                'x-forwarded-for' => '192.168.0.1'
            ])
        );
        $this->assertEquals('192.168.0.1', $request->getClientIP(), 'X-Forwarded-For');

        $request = Factory::newRequest(
            FakeRequest::createGetMethodRequestWithHeaders([
                'client-ip' => '192.168.0.1'
            ])
        );
        $this->assertEquals('192.168.0.1', $request->getClientIP(), 'Client-Ip');
    }

    public function testInvalidMethodException()
    {
        try {
            $this->request->withMethod('PUT, GET');
        } catch (\One\Protocol\Exceptions\InvalidMethodException $e) {
            $this->assertEquals($e->getRequest(), $this->request);
        }
    }

    #### POST Request ####

    public function testGetParsedBodyParam()
    {
        $request = $this->getPostRequest();

        $this->assertEquals('bar', $request->getParsedBodyParam('foo'), 'getParsedBodyParam');
        $this->assertEquals('bar', $request->getParam('foo'), 'getParam');
        $this->assertEquals('bar', $request->post('foo'), 'post');
    }

    public function testGetBody()
    {
        $request = $this->getPostRequest();
        $body = $request->getBody();

        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $body);
        $body->write('&zar=tar');
        $body->read(15);
        $this->assertEquals('foo=bar&zar=tar', (string) $request->getBody());
    }

    public function testJsonPost()
    {
        $request = $this->getPostJsonRequest();
        $this->assertEquals('bar', $request->post('foo'));
        $this->assertEquals('utf-8', $request->getContentCharset());
    }

    public function testXmlPost()
    {
        $request = $this->getPostXmlRequest();

        $this->assertEquals('bar', $request->getParsedBodyParam('foo'), 'XML:getParsedBodyParam');
        $this->assertEquals('bar', $request->getParam('foo'), 'XML:getParam');
        $this->assertEquals('bar', $request->post('foo'), 'XML:post');
    }

    public function testFormPost()
    {
        $request = $this->getPostFormRequest();

        $this->assertEquals('bar', $request->getParsedBodyParam('foo'), 'XML:getParsedBodyParam');
        $this->assertEquals('bar', $request->getParam('foo'), 'XML:getParam');
        $this->assertEquals('bar', $request->post('foo'), 'XML:post');
    }

    protected function getPostRequest()
    {
        return Factory::newRequest(
            FakeRequest::createPostMethodRequest()
        );
    }

    protected function getPostFormRequest()
    {
        return Factory::newRequest(
            FakeRequest::createPostFormRequest()
        );
    }

    protected function getPostJsonRequest()
    {
        return Factory::newRequest(
            FakeRequest::createPostJsonRequest()
        );
    }

    protected function getPostXmlRequest()
    {
        return Factory::newRequest(
            FakeRequest::createPostXMLRequest()
        );
    }

    #### Upload Files ####

    public function testUploadFile()
    {
        $request = $this->getUploadedFileRequest();

        $files = $request->getUploadedFiles();

        $this->assertTrue(count($files) === 1);
        $this->assertInstanceOf('One\Protocol\Message\UploadedFile', $files['file']);
    }

    protected function getUploadedFileRequest()
    {
        return Factory::newRequest(
            FakeRequest::createFileUploadRequest()
        );
    }

    #### Exceptions ####

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithRequestTargetException()
    {
        $this->request->withRequestTarget('foo bar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithProtocolVersionException()
    {
        $this->request->withProtocolVersion('3.0');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithHeaderException()
    {
        $this->request->withHeader(null, '');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithParsedBodyException()
    {
        $this->request->withParsedBody('foobar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithMethodException()
    {
        $this->request->withMethod(false);
    }

    /**
     * @expectedException \One\Protocol\Exceptions\InvalidMethodException
     */
    public function testWithMethodException2()
    {
        $this->request->withMethod('PUT, GET');
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
            [ 'withMethod',         [ null ] ],
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
                    new Uri('', 'domain.com')
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
