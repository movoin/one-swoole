<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Protocol\Protocols
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Protocol\Protocols;

use One\Protocol\Message\Uri;

class HttpProtocolTest extends ProtocolTester
{
    protected $protocolName = 'http';

    public function testInstance()
    {
        $this->assertInstanceOf(
            'One\\Protocol\\Contracts\\Protocol',
            $this->getProtocol()
        );
    }

    public function testJsonAccept()
    {
        $request = $this->newRequest();
        $response = $this->newResponse();

        $result = $this->getProtocol()->onRequest($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testXmlAccept()
    {
        $request = $this->newRequest('application/xml');
        $response = $this->newResponse();

        $result = $this->getProtocol()->onRequest($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testNotAcceptable()
    {
        $request = $this->newRequest('bad/accept');
        $response = $this->newResponse();

        $result = $this->getProtocol()->onRequest($request, $response);

        $this->assertEquals(406, $result->getStatusCode());
    }

    public function testMethodNotAllowed()
    {
        $request = $this->newRequest()
                        ->withMethod('PUT')
                        ->withUri(new Uri('http', 'foobar.com', null, '/test'));
        $response = $this->newResponse();

        $result = $this->getProtocol()->onRequest($request, $response);

        $this->assertEquals(405, $result->getStatusCode());
    }

    public function testNotFoundRoute()
    {
        $request = $this->newRequest()
                        ->withUri(new Uri('http', 'foobar.com', null, '/foo'));
        $response = $this->newResponse();

        $result = $this->getProtocol()->onRequest($request, $response);

        $this->assertEquals(404, $result->getStatusCode());
    }

    public function testNotFoundAction()
    {
        $request = $this->newRequest()
                        ->withUri(new Uri('http', 'foobar.com', null, '/bad'));
        $response = $this->newResponse();

        $result = $this->getProtocol()->onRequest($request, $response);

        $this->assertEquals(404, $result->getStatusCode());
    }

    public function testNoCache()
    {
        $request = $this->newRequest()->withHeader('Cache-Control', 'no-cache');
        $response = $this->newResponse();

        $result = $this->getProtocol()->onRequest($request, $response);

        $this->assertEquals('no-store,no-cache,must-revalidate', $result->getHeaderLine('Cache-Control'));
    }

    public function testSetGzip()
    {
        $request = $this->newRequest()->withHeader('Accept-Encoding', 'gzip');
        $response = $this->newResponse();

        $result = $this->getProtocol()->onRequest($request, $response);

        $this->assertEquals('gzip', $result->getHeaderLine('Content-Encoding'));
    }

    public function testBadMiddlewareHandler()
    {
        $this->middlewares = [
            'group' => [
                'all' => [
                    'One\\Tests\\Fixtures\\Middlewares\\OneFilter',
                ]
            ],
            'match' => [
                '*' => 'all',
                '/bad/middleware/handler' => 'One\\Tests\\Fixtures\\Middlewares\\BadFilter',
            ]
        ];
        $request = $this->newRequest()
                        ->withUri(new Uri('http', 'foobar.com', null, '/bad/middleware/handler'));
        $response = $this->newResponse();

        $result = $this->getProtocol()->onRequest($request, $response);

        $this->assertEquals(500, $result->getStatusCode());
    }

    public function testBadMiddleware()
    {
        $this->middlewares = [
            'group' => [
                'all' => [
                    'One\\Tests\\Fixtures\\Middlewares\\OneFilter',
                ]
            ],
            'match' => [
                '*' => 'all',
                '/bad/middleware' => 'One\\Tests\\Fixtures\\Middlewares\\OneFilter',
            ]
        ];
        $request = $this->newRequest()
                        ->withUri(new Uri('http', 'foobar.com', null, '/bad/middleware/handler', 'return=1'));
        $response = $this->newResponse();

        $result = $this->getProtocol()->onRequest($request, $response);

        $this->assertEquals(404, $result->getStatusCode());
    }

    public function testProtocolTraits()
    {
        $this->assertEquals([], $this->getProtocol()->getServerStartItems());
        $this->assertEquals([], $this->getProtocol()->getWorkerStartItems());
    }
}
