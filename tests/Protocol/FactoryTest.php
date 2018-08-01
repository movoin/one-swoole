<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Protocol
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Protocol;

use One\Protocol\Factory;
use One\Protocol\Exceptions\ProtocolException;

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @expectedException \One\Protocol\Exceptions\ProtocolException
     */
    public function testProtocolException()
    {
        Factory::newProtocol('BadProtocol');
    }

    public function testProtocolExceptionMethod()
    {
        try {
            Factory::newProtocol('BadProtocol');
        } catch (ProtocolException $e) {
            $this->assertEquals('BadProtocol', $e->getProtocol());
        }
    }

    public function testNewProtocol()
    {
        $protocol = Factory::newProtocol('http');
        $this->assertInstanceOf('One\\Protocol\\Contracts\\Protocol', $protocol);
    }

    public function testNewStream()
    {
        // String
        $stream = Factory::newStream('test');
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $stream);

        // Null
        $stream = Factory::newStream();
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $stream);

        // Resource
        $stream = Factory::newStream(fopen('php://temp', 'w+'));
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $stream);

        // StreamInterface
        $stream = Factory::newStream(Factory::newStream('test'));
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $stream);
    }
}
