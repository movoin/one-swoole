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

use One\Support\Helpers\Assert;

class AssertTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideTestTrue
     */
    public function testTrue($method, $value)
    {
        $this->assertTrue(Assert::$method($value));
    }

    public function provideTestTrue()
    {
        return [
            [ 'string',           'foo' ],
            [ 'stringNotEmpty',   'foo' ],
            [ 'integer',          100 ],
            [ 'float',            1.1 ],
            [ 'numeric',          '10' ],
            [ 'natural',          99 ],
            [ 'boolean',          true ],
            [ 'object',           new \stdClass ],
            [ 'callable',         function () { return true; } ],
            [ 'array',            ['foo'] ],
            [ 'ip',               '127.0.0.1' ],
            [ 'url',              'http://domain.com' ],
            [ 'json',             '{"foo": "bar"}' ],
            [ 'namespace',        'foo\\bar\\zar' ]
        ];
    }

    /**
     * @dataProvider provideTestFalse
     */
    public function testFalse($method, $value)
    {
        $this->assertFalse(Assert::$method($value));
    }

    public function provideTestFalse()
    {
        return [
            [ 'string',           1 ],
            [ 'stringNotEmpty',   '' ],
            [ 'integer',          '100' ],
            [ 'float',            1 ],
            [ 'numeric',          'hello' ],
            [ 'natural',          '99' ],
            [ 'boolean',          'true' ],
            [ 'object',           'new \stdClass' ],
            [ 'callable',         'function () { return true; }' ],
            [ 'array',            'foo' ],
            [ 'ip',               'localhost' ],
            [ 'url',              'vvvom' ],
            [ 'json',             "{'foo': 'bar'}" ],
            [ 'namespace',        'foobar' ]
        ];
    }

    public function testResource()
    {
        $tmp = tmpfile();
        $this->assertTrue(Assert::resource($tmp));
        fclose($tmp);

        $tmp = tmpfile();
        $this->assertTrue(Assert::resource($tmp, 'stream'));
        fclose($tmp);
    }

    public function testCountable()
    {
        $this->assertTrue(Assert::countable([]));
        $this->assertTrue(Assert::countable(new \One\Support\Collection));
    }

    public function testIterable()
    {
        $this->assertTrue(Assert::iterable([]));
        $this->assertTrue(Assert::iterable(new \One\Support\Collection));
    }

    public function testInstanceOf()
    {
        $this->assertTrue(Assert::instanceOf(new \One\Support\Collection, '\\One\\Support\\Contracts\\Arrayable'));
    }

    public function testInstanceOfAny()
    {
        $this->assertTrue(Assert::instanceOfAny(
            new \One\Support\Collection,
            [
                '\\One\\Support\\Contracts\\Hashable',
                '\\One\\Support\\Contracts\\Arrayable'
            ]
        ));

        $this->assertFalse(Assert::instanceOfAny(
            new \One\Support\Collection,
            [
                '\\One\\Support\\Contracts\\Hashable'
            ]
        ));
    }

    public function testRange()
    {
        $this->assertTrue(Assert::range(10, 0, 100));
    }

    public function testOneOf()
    {
        $this->assertTrue(Assert::oneOf('foo', ['tar', 'zar', 'bar', 'foo']));
    }

    public function testContains()
    {
        $this->assertTrue(Assert::contains('hello world', 'or'));
    }

    public function testEmail()
    {
        $this->assertTrue(Assert::email('movoin@gmail.com'));
        $this->assertTrue(Assert::email('movoin@gmail.com', 'gmail.com'));

        $this->assertFalse(Assert::email('movoin[at]gmail.com'));
        $this->assertFalse(Assert::email('movoin@gmail.com', 'qq.com'));
    }

    public function testStartsWith()
    {
        $this->assertTrue(Assert::startsWith('@cmd', '@'));
        $this->assertFalse(Assert::startsWith('#cmd', '@'));
    }
}
