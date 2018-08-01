<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Protocol\Exceptions
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Protocol\Exceptions;

class ProtocolExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideExceptionMethods
     */
    public function testExceptions($method, $params, $code)
    {
        $exception = forward_static_call_array(
            ['One\\Protocol\\Exceptions\\ProtocolException', $method],
            $params
        );

        $this->assertEquals($code, $exception->getCode());
    }

    public function provideExceptionMethods()
    {
        return [
            [ 'badRequest', ['bad'], 400 ],
            [ 'unauthorized', ['bad'], 401 ],
            [ 'forbidden', [], 403 ],
            [ 'notFound', ['bad'], 404 ],
            [ 'methodNotAllowed', ['GET', '/path'], 405 ],
            [ 'notAcceptable', ['application/json'], 406 ],
            [ 'tooManyRequests', [], 429 ],
        ];
    }
}
