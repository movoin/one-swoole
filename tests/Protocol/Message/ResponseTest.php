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
use One\Tests\Protocol\Message\Swoole\Response as FakeResponse;

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    protected $response;

    public function setUp()
    {
        $this->response = Factory::newHttpResponse(FakeResponse::newResponse());
    }

    public function tearDown()
    {
        $this->response = null;
    }

    public function testInstanceOf()
    {
        $response = $this->response->withHeader('foo', 'bar');
        $response->end();

        $this->assertInstanceOf('One\\Protocol\\Contracts\\Response', $response);
    }

    public function testGetStatusCode()
    {
        $this->assertEquals(200, $this->response->getStatusCode());

        $response = $this->response->withStatus(300);
        $this->assertEquals(300, $response->getStatusCode());
    }

    public function testGetReasonPhrase()
    {
        $this->assertEquals('OK', $this->response->getReasonPhrase());

        $response = $this->response->withStatus(200);
        $this->assertEquals('OK', $response->getReasonPhrase());

        $response = $this->response->withStatus(400, 'My Bad');
        $this->assertEquals('My Bad', $response->getReasonPhrase());
    }

    public function testWithHeader()
    {
        $response = $this->response->withHeader('location', 'foobar');

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testWrite()
    {
        $this->response->write('foo');

        $this->assertEquals('foo', (string) $this->response->getBody());
    }

    public function testWithResponse()
    {
        $swoole = new \Swoole\Http\Response();

        $response = $this->response->withSwooleResponse($swoole);
        $this->assertInstanceOf('Swoole\Http\Response', $response->getSwooleResponse());

        $response = $response->withSwooleResponse($swoole);
        $this->assertInstanceOf('Swoole\Http\Response', $response->getSwooleResponse());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithStatusLessThan100Exception()
    {
        $this->response->withStatus(50);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithStatusGreaterThan600Exception()
    {
        $this->response->withStatus(600);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithStatusNotIntegerException()
    {
        $this->response->withStatus('Ok');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithStatusReasonNotStringException()
    {
        $this->response->withStatus(300, false);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithStatusReasonEmptyException()
    {
        $this->response->withStatus(209);
    }
}
