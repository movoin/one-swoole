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

use One\Support\Collection;
use One\Support\Helpers\Json;

class CollectionTest extends \PHPUnit\Framework\TestCase
{
    protected $collect;

    protected $defaults = [
        'foo' => 'oof',
        'bar' => 'rab',
        'zar' => 'raz',
    ];

    public function setUp()
    {
        $this->collect = new Collection($this->defaults);
    }

    public function tearDown()
    {
        $this->collect = null;
    }

    public function testGetAll()
    {
        $this->assertSame($this->defaults, $this->collect->all());
    }

    public function testGetKeys()
    {
        $this->assertSame(array_keys($this->defaults), $this->collect->keys());
    }

    public function testReplace()
    {
        $this->collect->replace([
            'foo' => 'oof'
        ]);
        $this->assertNotSame($this->defaults, $this->collect->all());
    }

    public function testUpdate()
    {
        $update = [
            'foo' => 'foo',
            'bar' => 'bar',
            'zar' => 'zar'
        ];
        $this->collect->update($update);
        $this->assertSame($update, $this->collect->all());
    }

    public function testMap()
    {
        $result = $this->collect->map(function ($value, $key) {
            return "${value}x";
        });

        $data = [
            'foo' => 'oofx',
            'bar' => 'rabx',
            'zar' => 'razx',
        ];

        $this->assertSame($result, $data);
    }

    public function testFilter()
    {
        $this->assertSame(['foo' => 'oof'], $this->collect->filter(function ($value) {
            return $value === 'oof';
        }));
    }

    public function testCount()
    {
        $this->assertEquals(3, $this->collect->count());
    }

    public function testClear()
    {
        $this->collect->clear();
        $this->assertEquals(0, $this->collect->count());
    }

    public function testHas()
    {
        $this->assertTrue($this->collect->has('foo'));
    }

    public function testGetValue()
    {
        $this->assertEquals('oof', $this->collect->get('foo'));
        $this->assertNull($this->collect->get('tar'));
    }

    public function testGetByValue()
    {
        $this->collect->replace([
            [
                'id' => 1,
                'type' => 'foo'
            ],
            [
                'id' => 2,
                'type' => 'bar'
            ]
        ]);

        $this->assertSame([ 0 => [
                        'id' => 1,
                        'type' => 'foo'
                ]], $this->collect->getByValue('type', 'foo'));
    }

    public function testSetValue()
    {
        $this->collect->set('foo', 'xxx');
        $this->assertEquals('xxx', $this->collect->get('foo'));
    }

    public function testPushItem()
    {
        $this->collect->push('tar');
        $this->assertEquals(4, $this->collect->count());
    }

    public function testPushMany()
    {
        $this->collect->pushMany(['tar', 'jar']);
        $this->assertEquals(5, $this->collect->count());
    }

    public function testShift()
    {
        $this->assertEquals('oof', $this->collect->shift());
    }

    public function testPop()
    {
        $this->assertEquals('raz', $this->collect->pop());
    }

    public function testArrayAccess()
    {
        $this->collect['bar'] = true;
        $this->assertTrue($this->collect->get('bar'));

        $this->collect[] = 'push';
        $this->assertEquals(4, $this->collect->count());

        $this->assertTrue(isset($this->collect['bar']));

        unset($this->collect['bar']);
        $this->assertFalse($this->collect->has('bar'));

        $this->assertEquals('raz', $this->collect['zar']);

        $this->assertEquals(null, $this->collect['bar']);
    }

    public function testArrayIterator()
    {
        $this->assertInstanceOf('ArrayIterator', $this->collect->getIterator());
    }

    public function testToArray()
    {
        $this->assertSame($this->defaults, $this->collect->toArray());
    }

    public function testToJson()
    {
        $this->assertEquals(Json::encode($this->defaults), $this->collect->toJson());
    }
}
