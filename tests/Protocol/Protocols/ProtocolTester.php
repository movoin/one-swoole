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

use One\Event\Emitter;
use One\Logging\Logger;
use One\Middleware\Manager;
use One\Protocol\Factory;
use One\Protocol\Message\Cookies;
use One\Protocol\Message\Headers;
use One\Protocol\Message\Request;
use One\Protocol\Message\Uri;
use One\Protocol\Server;
use One\Routing\Router;

class ProtocolTester extends \PHPUnit\Framework\TestCase
{
    protected $protocolName = 'http';
    protected $middlewares = [];

    private $server;
    private $protocol;

    public function setUp()
    {
        $this->createProtocol();
    }

    public function tearDown()
    {
        $this->protocolName = 'http';
        $this->server = null;
        $this->protocol = null;
        $this->middlewares = [];

        @unlink(RUNTIME_PATH . "/{$this->protocolName}.log");
    }

    protected function getServer()
    {
        return $this->server;
    }

    protected function getProtocol()
    {
        return $this->protocol;
    }

    protected function createProtocol()
    {
        $this->createServer();

        $this->protocol = Factory::newProtocol($this->protocolName);
        $this->protocol->setServer($this->server);
    }

    protected function createServer()
    {
        $this->server = new Server('test', $this->protocolName);

        // Event
        $this->server->bind('event', function ($server) {
            return new Emitter;
        });

        // Logging
        $this->server->bind('logger', function ($server) {
            return new Logger(
                $this->protocolName,
                RUNTIME_PATH . "/{$this->protocolName}.log"
            );
        });

        // Routing
        $this->server->bind('router', function ($server) {
            $router = new Router;
            $router->addRoute(
                'GET',
                '/test',
                'One\\Tests\\Fixtures\\App\\TestAction'
            );
            $router->addRoute(
                'GET',
                '/bad',
                'One\\Tests\\Fixtures\\App\\BadAction'
            );
            $router->addRoute(
                'GET',
                '/bad/middleware',
                'One\\Tests\\Fixtures\\App\\TestAction'
            );
            $router->addRoute(
                'GET',
                '/bad/middleware/handler',
                'One\\Tests\\Fixtures\\App\\TestAction'
            );

            return $router;
        });

        // Middleware
        $this->server->bind('middleware', function ($server) {
            return new Manager($this->middlewares);
        });
    }

    protected function newRequest($accept = 'application/json')
    {
        $request = new Request(
            'GET',
            new Uri('http', 'foobar.com', null, '/test'),
            new Headers,
            new Cookies,
            [],
            Factory::newStream('')
        );

        return $request
            ->withProtocol($this->protocolName)
            ->withHeader('Accept', $accept)
        ;
    }

    protected function newResponse()
    {
        return (new FakeResponse)->withProtocol($this->protocolName);
    }
}
