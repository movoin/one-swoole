<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Routing
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Routing;

use One\Routing\Router;
use One\Protocol\Factory;
use One\Protocol\Message\Uri;
use One\Protocol\Message\Cookies;
use One\Protocol\Message\Headers;
use One\Protocol\Message\Request;

class RouterTest extends \PHPUnit\Framework\TestCase
{
    protected $router;

    public function setUp()
    {
        $this->router = new Router;
        $this->router->addRoute('GET', '/test', 'handler');
    }

    public function tearDown()
    {
        $this->router = null;
    }

    public function testMatch()
    {
        $request = new Request(
            'GET',
            new Uri('http', 'foobar.com', null, '/test'),
            new Headers,
            new Cookies,
            [],
            Factory::newStream('')
        );

        $this->assertEquals([Router::FOUND, 'handler', []], $this->router->match($request->withProtocol('http')));
        $this->assertEquals([Router::FOUND, 'handler', []], $this->router->match($request->withProtocol('tcp')));

        $request = $request->withUri(
            new Uri('http', 'foobar.com', null, '/foo')
        );
        $this->assertEquals([Router::NOT_FOUND], $this->router->match($request->withProtocol('tcp')));
    }
}
