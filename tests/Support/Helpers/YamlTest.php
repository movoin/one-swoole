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

use One\Support\Helpers\Yaml;

class YamlTest extends \PHPUnit\Framework\TestCase
{
    public function testParseFile()
    {
        $this->assertNull(Yaml::parseFile('not_found.yml'));
        $yaml = Yaml::parseFile(__DIR__ . '/testfile/test.yml');
        $this->assertSame($yaml, ['foo' => 'bar']);
    }

    public function testParse()
    {
        $yaml = 'foo: bar';
        $this->assertSame(Yaml::parse($yaml), ['foo' => 'bar']);
    }

    public function testDump()
    {
        $this->assertSame(Yaml::dump(['foo' => 'bar']), '{ foo: bar }');
    }
}
