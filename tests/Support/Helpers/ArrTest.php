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

use One\Support\Collection;
use One\Support\Helpers\Arr;

class ArrTest extends \PHPUnit\Framework\TestCase
{
    public function testExists()
    {
        $this->assertTrue(Arr::exists(['foo' => 'bar'], 'foo'));
        $this->assertTrue(Arr::exists(new Collection(['foo' => 'bar']), 'foo'));
    }

    public function testGetValue()
    {
        $this->assertNull(Arr::get('foo', 'bar'));
        $this->assertEquals(['foo' => 'bar'], Arr::get(['foo' => 'bar'], ''));
        $this->assertEquals('bar', Arr::get(['foo' => 'bar'], 'foo'));

        $this->assertEquals(
            'zar',
            Arr::get([
                'foo' => [
                    'bar' => 'zar'
                ]
            ], 'foo.bar')
        );
    }

    public function testFilter()
    {
        $this->assertEquals(
            ['bar' => 'zar'],
            Arr::filter(
                [
                    'foo' => 'bar',
                    'bar' => 'zar',
                    'zar' => 'tar'
                ],
                function ($val) {
                    return $val === 'zar';
                }
            )
        );
    }

    public function testDiff()
    {
        $before = [ 'foo' => 'bar' ];
        $after  = [ 'foo' => 'zar', 'zar' => 'tar' ];

        $this->assertEquals($after, Arr::diff($before, $after));
    }

    public function testRemoveByValue()
    {
        $array = Arr::removeByValue([
            'foo' => 'foo',
            'bar' => 'bar',
            'zar' => ' '
        ], '');

        $this->assertFalse(Arr::exists($array, 'zar'));
    }

    public function testGroupBy()
    {
        $array = Arr::groupBy([
            [ 'name' => 'name1', 'type' => 'type1' ],
            [ 'name' => 'name2', 'type' => 'type1' ],
            [ 'name' => 'name3', 'type' => 'type2' ]
        ], 'type');

        $this->assertEquals($array, [
            'type1' => [
                [ 'name' => 'name1', 'type' => 'type1' ],
                [ 'name' => 'name2', 'type' => 'type1' ]
            ],
            'type2' => [
                [ 'name' => 'name3', 'type' => 'type2' ]
            ]
        ]);
    }

    public function testColumns()
    {
        $this->assertEquals([], Arr::columns([], ''));

        $array = Arr::columns([
            [ 'name' => 'name1', 'type' => 'type1' ],
            [ 'name' => 'name2', 'type' => 'type1' ],
            [ 'name' => 'name3', 'type' => 'type2' ]
        ], 'name');

        $this->assertEquals($array, [
            'name1',
            'name2',
            'name3'
        ]);
    }

    public function testHashMap()
    {
        $array = Arr::hashMap([
            [ 'name' => 'name1', 'type' => 'type1' ],
            [ 'name' => 'name2', 'type' => 'type1' ],
            [ 'name' => 'name3', 'type' => 'type2' ]
        ], 'name ', ' type');

        $this->assertEquals($array, [
            'name1' => 'type1',
            'name2' => 'type1',
            'name3' => 'type2'
        ]);

        $array = Arr::hashMap([
            [ 'name' => 'name1', 'type' => 'type1' ],
            [ 'name' => 'name2', 'type' => 'type1' ],
            [ 'name' => 'name3', 'type' => 'type2' ]
        ], 'name ');

        $this->assertEquals($array, [
            'name1' => [ 'name' => 'name1', 'type' => 'type1' ],
            'name2' => [ 'name' => 'name2', 'type' => 'type1' ],
            'name3' => [ 'name' => 'name3', 'type' => 'type2' ]
        ]);
    }

    public function testConvert()
    {
        $obj = new \stdClass;
        $obj->name = 'foo';
        $obj->value = 'bar';

        $array = [
            'name' => 'foo',
            'value' => 'bar'
        ];

        $this->assertEquals($array, Arr::convertFromStdClass($obj));
        $this->assertEquals($obj, Arr::convertToStdClass($array));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDiffBeforeException()
    {
        Arr::diff(null, []);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDiffAfterException()
    {
        Arr::diff([], null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRemoveByValueException()
    {
        Arr::removeByValue(null, null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGroupByException()
    {
        Arr::groupBy(null, 'null');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testColumnsException()
    {
        Arr::columns(null, 'null');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testHashMapException()
    {
        Arr::hashMap(null, null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConvertToStdClassException()
    {
        Arr::convertToStdClass(null);
    }
}
