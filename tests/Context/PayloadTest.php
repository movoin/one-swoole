<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Context
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Context;

use One\Context\Payload;

class PayloadTest extends \PHPUnit\Framework\TestCase
{
    protected $payload;

    public function setUp()
    {
        $this->payload = new Payload;
    }

    public function tearDown()
    {
        $this->payload = null;
    }

    /**
     * @dataProvider provideGetMethods
     */
    public function testGet($method, $result)
    {
        $getMethod = 'get' . ucfirst($method);

        $this->assertEquals(
            call_user_func_array([$this->payload, $getMethod], []),
            $result
        );
    }

    public function provideGetMethods()
    {
        return [
            [ 'code',       200 ],
            [ 'message',    '' ],
            [ 'data',       '' ],
            [ 'error',      '' ],
            [ 'options',    [] ],
        ];
    }

    /**
     * @dataProvider provideWithMethods
     */
    public function testWith($method, $attributes, $result)
    {
        $withMethod = 'with' . ucfirst($method);
        $getMethod  = 'get' . ucfirst($method);

        $payload = call_user_func_array([$this->payload, $withMethod], $attributes);

        $this->assertEquals(
            $payload->$getMethod(),
            $result
        );
    }

    public function provideWithMethods()
    {
        return [
            [ 'code',       [ 300 ],        300 ],
            [ 'message',    ['foo'],      'foo' ],
            [ 'data',       ['bar'],      'bar' ],
            [ 'error',      ['tar'],      'tar' ],
            [ 'options',    [['zar' => 1]],    ['zar' => 1] ],
        ];
    }

    public function testToArray()
    {
        $data = [
            'code' => 200,
            'message' => 'ok',
            'error' => 'none',
            'data' => [
                'foo' => 'bar',
            ],
            'tar' => 'zar'
        ];

        $payload = $this->payload->withMessage('ok')
                                ->withError('none')
                                ->withData(['foo' => 'bar'])
                                ->withOptions(['tar' => 'zar']);

        $this->assertEquals($data, $payload->toArray());
    }

    public function testToJson()
    {
        $json = '{"code":200,"message":"ok"}';
        $json2 = '{"code":200}';

        $payload = $this->payload->withMessage('ok')->withData(null)->withError('')->withOptions([])->withCode(200);

        $this->assertEquals($json, $payload->toJson());

        $payload = $this->payload->withMessage('');

        $this->assertEquals($json2, $payload->toJson());
    }
}
