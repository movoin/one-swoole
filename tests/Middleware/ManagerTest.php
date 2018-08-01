<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Middleware
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Middleware;

use One\Middleware\Manager;
use One\Middleware\Exceptions\MiddlewareException;
use One\Protocol\Factory;
use One\Protocol\Message\Uri;
use One\Protocol\Message\Cookies;
use One\Protocol\Message\Headers;
use One\Protocol\Message\Request;
use One\Protocol\Message\Response;

class ManagerTest extends \PHPUnit\Framework\TestCase
{
    protected $manager;

    public function setUp()
    {
        $this->manager = new Manager([
            'group' => [
                'all' => [
                    'One\\Tests\\Fixtures\\Middlewares\\OneFilter',
                    'One\\Tests\\Fixtures\\Middlewares\\OneInterceptor',
                    'One\\Tests\\Fixtures\\Middlewares\\OneTerminator',
                ]
            ],
            'match' => [
                '*' => 'all',
                '/filter' => 'One\\Tests\\Fixtures\\Middlewares\\OneFilter',
                '/interceptor' => 'One\\Tests\\Fixtures\\Middlewares\\OneInterceptor',
                '/terminator' => 'One\\Tests\\Fixtures\\Middlewares\\OneTerminator',
            ]
        ]);
    }

    public function tearDown()
    {
        $this->manager = null;
    }

    public function testFilter()
    {
        $request = $this->getRequest('/filter');
        $response = $this->getResponse();

        $this->manager->matchRequest($request);

        $this->assertEquals(
            404,
            $this->manager->executeFilters($request, $response)->getStatusCode()
        );
    }

    public function testFilterNull()
    {
        $request = $this->getRequest('/filter')->withAttribute('return', 1);
        $response = $this->getResponse();

        $this->manager->matchRequest($request);

        $this->assertEquals(
            null,
            $this->manager->executeFilters($request, $response)
        );
    }

    public function testInterceptor()
    {
        $request = $this->getRequest('/interceptor');
        $response = $this->getResponse();

        $this->manager->matchRequest($request);

        $return = $this->manager->executeInterceptors($request, $response);

        $this->assertSame([$request, $response], $return);
    }

    public function testTerminator()
    {
        $request = $this->getRequest('/terminator');
        $response = $this->getResponse();

        $this->manager->matchRequest($request);

        $return = $this->manager->executeTerminators($request, $response);

        $this->assertSame($response, $return);
    }

    /**
     * @expectedException One\Middleware\Exceptions\MiddlewareException
     */
    public function testException()
    {
        $manager = new Manager([
            'match' => [
                '/bad' => 'One\\Tests\\Middleware\\Fixtures\\OneBadFilter',
            ]
        ]);

        $request = $this->getRequest('/bad');
        $manager->matchRequest($request);
    }

    public function testExceptionGetter()
    {
        $manager = new Manager([
            'match' => [
                '/bad' => 'One\\Tests\\Middleware\\Fixtures\\OneBadFilter',
            ]
        ]);

        $request = $this->getRequest('/bad');

        try {
            $manager->matchRequest($request);
        } catch (MiddlewareException $e) {
            $this->assertEquals(
                'One\\Tests\\Middleware\\Fixtures\\OneBadFilter',
                $e->getMiddleware()
            );
        }
    }

    protected function getRequest($path)
    {
        return new Request(
            'GET',
            new Uri('http', 'foobar.com', null, $path),
            new Headers,
            new Cookies,
            [],
            Factory::newStream('')
        );
    }

    protected function getResponse()
    {
        return new Response;
    }
}
