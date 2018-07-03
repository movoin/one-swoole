<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Support\Helpers
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Support\Helpers;

use One\Support\Helpers\Json;

class JsonTest extends \PHPUnit\Framework\TestCase
{
    public function testEncode()
    {
        $json = '{"foo":"bar","int":1,"float":1.1,"unicode":"中文"}';

        $this->assertEquals($json, Json::encode([
            'foo' => 'bar',
            'int' => 1,
            'float' => 1.1,
            'unicode' => '中文'
        ]));
    }

    /**
     * @expectedException One\Support\Exceptions\JsonException
     */
    public function testEncodeException()
    {
        Json::encode("\xB1\x31");
    }

    public function testDecode()
    {
        $array = [
            'foo' => 'bar',
            'int' => 1,
            'float' => 1.1,
            'unicode' => '中文'
        ];

        $this->assertEquals(Json::decode('{"foo":"bar","int":1,"float":1.1,"unicode":"中文"}'), $array);
    }

    /**
     * @expectedException One\Support\Exceptions\JsonException
     */
    public function testDecodeException()
    {
        Json::decode("{'foo': 'bar'}");
    }
}
