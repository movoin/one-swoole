<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Support
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Support;

use One\Support\Container;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    protected $container;

    public function setUp()
    {
        $this->container = new Container;
    }

    public function tearDown()
    {
        $this->container = null;
    }

    public function testInstanceOfPSR11()
    {
        $this->assertInstanceOf('Psr\Container\ContainerInterface', $this->container);
    }

    public function testSetAlias()
    {
        $this->container->alias('foo', 'bar');
        $this->assertTrue($this->container->has('bar'));
    }
}
