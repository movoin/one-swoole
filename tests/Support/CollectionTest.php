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
}
