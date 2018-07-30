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

    public function testOnRequest()
    {
        $request = $this->newRequest();
        $response = $this->newResponse();

        $result = $this->getProtocol()->onRequest($request, $response);

        $this->assertInstanceOf('One\\Protocol\\Contracts\\Response', $result);
    }
}
