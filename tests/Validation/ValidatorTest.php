<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Validation
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Validation;

use One\Validation\Validator;

class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    private $validator;

    public function setUp()
    {
        $this->validator = new Validator;
    }

    public function tearDown()
    {
        $this->validator = null;
    }

    /**
     * @dataProvider provideRules
     */
    public function testValidateTrue(array $attributes, array $rule)
    {
        $this->validator->reset();
        $this->validator->addRules([$rule]);

        $this->assertTrue($this->validator->validate($attributes));
        $this->assertEquals(0, count($this->validator->getErrors()));
        $this->assertEmpty($this->validator->getLastError());
    }

    public function provideRules()
    {
        return [
            [
                [ 'name' => 'string' ],
                [ 'name', 'required' ]
            ],
            [
                [ 'name' => 'string' ],
                [ 'name', 'str' ]
            ],
            [
                [ 'name' => 'string' ],
                [ 'name', 'string' ]
            ],
            [
                [ 'name' => '1024' ],
                [ 'name', 'num' ]
            ],
            [
                [ 'name' => '1024' ],
                [ 'name', 'numeric' ]
            ],
            [
                [ 'name' => '1024' ],
                [ 'name', 'number' ]
            ],
            [
                [ 'name' => 1024 ],
                [ 'name', 'int' ]
            ],
            [
                [ 'name' => 1024 ],
                [ 'name', 'integer' ]
            ],
            [
                [ 'name' => '1024qwertyQWERTY' ],
                [ 'name', 'alphanumeric' ]
            ],
            [
                [ 'name' => 10.24 ],
                [ 'name', 'float' ]
            ],
            [
                [ 'name' => [ '10', '24' ] ],
                [ 'name', 'array' ]
            ],
            [
                [ 'name' => '{"foo":"bar"}' ],
                [ 'name', 'json' ]
            ],
            [
                [ 'name' => null ],
                [ 'name', 'null' ]
            ],
            [
                [ 'name' => 'null' ],
                [ 'name', 'notNull' ]
            ],
            [
                [ 'name' => false ],
                [ 'name', 'bool' ]
            ],
            [
                [ 'name' => true ],
                [ 'name', 'boolean' ]
            ],
            [
                [ 'name' => '2018-07-13 23:59:59' ],
                [ 'name', 'date' ]
            ],
            [
                [ 'name' => '2018-07-13 23:59:59' ],
                [ 'name', 'datetime' ]
            ],
            [
                [ 'name' => new \DateTime() ],
                [ 'name', 'datetime' ]
            ],
            [
                [ 'name' => 'movoin@gmail.com' ],
                [ 'name', 'email' ]
            ],
            [
                [ 'name' => 'movoin@gmail.com' ],
                [ 'name', 'email', 'domain' => 'gmail.com' ]
            ],
            [
                [ 'name' => 'https://www.gmail.com' ],
                [ 'name', 'url' ]
            ],
            [
                [ 'name' => '8.8.8.8' ],
                [ 'name', 'ip' ]
            ],
            [
                [ 'name' => '13400000001' ],
                [ 'name', 'mobile' ]
            ],
            [
                [ 'name' => '010-88668866' ],
                [ 'name', 'phone' ]
            ],
            [
                [ 'name' => 64 ],
                [ 'name', 'between', 'min' => 0, 'max' => 128 ]
            ],
            [
                [ 'name' => 'foo' ],
                [ 'name', 'in', 'range' => [ 'foo', 'bar' ] ]
            ],
            [
                [ 'name' => 'zar' ],
                [ 'name', 'notIn', 'range' => [ 'foo', 'bar' ] ]
            ],
            [
                [ 'name' => 'zar' ],
                [ 'name', 'len', 'is' => 3 ]
            ],
            [
                [ 'name' => '中文' ],
                [ 'name', 'length', 'is' => 2 ]
            ],
            [
                [ 'name' => '中文' ],
                [ 'name', 'length', 'min' => 2 ]
            ],
            [
                [ 'name' => '中文' ],
                [ 'name', 'len', 'max' => 4 ]
            ],
            [
                [ 'name' => '中文' ],
                [ 'name', 'length', 'min' => 2, 'max' => 4 ]
            ],
            [
                [ 'name' => 5 ],
                [ 'name', 'greater', 'than' => 2 ]
            ],
            [
                [ 'name' => 5 ],
                [ 'name', 'less', 'than' => 10 ]
            ],
            [
                [ 'name' => 10 ],
                [ 'name', 'equals', 'to' => 10 ]
            ],
            [
                [ 'name' => 10 ],
                [ 'name', 'notEquals', 'to' => 100 ]
            ],
            [
                [ 'name' => new \stdClass ],
                [ 'name', 'instance', 'of' => \stdClass::class ]
            ],
            [
                [ 'name' => 'Ab' ],
                [ 'name', 'regex', 'pattern' => '/^[A-Za-z]+$/' ]
            ],
        ];
    }

    /**
     * @dataProvider provideFailsRules
     */
    public function testValidateFalse(array $attributes, array $rule, string $message)
    {
        $this->validator->reset();
        $this->validator->addRules([$rule]);

        $this->assertFalse($this->validator->validate($attributes));
        $this->assertGreaterThan(0, count($this->validator->getErrors()));
        $this->assertEquals($message, $this->validator->getLastError());
    }

    public function provideFailsRules()
    {
        return [
            [
                [ ],
                [ 'name', 'required' ],
                'name 必须填写'
            ],
            [
                [ 'name' => false ],
                [ 'name', 'str', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => false ],
                [ 'name', 'string', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'foobar' ],
                [ 'name', 'num', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'foobar' ],
                [ 'name', 'numeric', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'foobar' ],
                [ 'name', 'number', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'foobar' ],
                [ 'name', 'int', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => '1024' ],
                [ 'name', 'integer', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => false ],
                [ 'name', 'alphanumeric', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => '1024' ],
                [ 'name', 'float', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'string' ],
                [ 'name', 'array', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => '{foo: bar}' ],
                [ 'name', 'json', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'null' ],
                [ 'name', 'null', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => null ],
                [ 'name', 'notNull', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 1 ],
                [ 'name', 'bool', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 0 ],
                [ 'name', 'boolean', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'foobar' ],
                [ 'name', 'date', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'foobar' ],
                [ 'name', 'datetime', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'movoin#gmail.com' ],
                [ 'name', 'email', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'movoin@gmail.com' ],
                [ 'name', 'email', 'domain' => 'qq.com', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'www.gmail.com' ],
                [ 'name', 'url', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'x.8.8.8' ],
                [ 'name', 'ip', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => '134000000019' ],
                [ 'name', 'mobile', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => '010-886688661' ],
                [ 'name', 'phone', 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 129 ],
                [ 'name', 'between', 'min' => 0, 'max' => 128, 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'zar' ],
                [ 'name', 'in', 'range' => [ 'foo', 'bar' ], 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'foo' ],
                [ 'name', 'notIn', 'range' => [ 'foo', 'bar' ], 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 'foobar' ],
                [ 'name', 'len', 'is' => 3, 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => '中文字' ],
                [ 'name', 'length', 'is' => 2, 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => '中' ],
                [ 'name', 'length', 'min' => 2, 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => '中文中文中文' ],
                [ 'name', 'len', 'max' => 4, 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => '中' ],
                [ 'name', 'length', 'min' => 2, 'max' => 4, 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 1 ],
                [ 'name', 'greater', 'than' => 2, 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 11 ],
                [ 'name', 'less', 'than' => 10, 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 11 ],
                [ 'name', 'equals', 'to' => 10, 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => 100 ],
                [ 'name', 'notEquals', 'to' => 100, 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => new \stdClass ],
                [ 'name', 'instance', 'of' => Validator::class, 'message' => 'test' ],
                'test'
            ],
            [
                [ 'name' => '1024' ],
                [ 'name', 'regex', 'pattern' => '/^[A-Za-z]+$/', 'message' => 'test' ],
                'test'
            ],
        ];
    }

    /**
     * @dataProvider provideIgnoreUndefined
     */
    public function testIgnoreUndefined(array $attributes, array $rule)
    {
        $this->validator->reset();
        $this->validator->addRules([$rule]);

        $this->assertTrue($this->validator->validate($attributes));
    }

    public function provideIgnoreUndefined()
    {
        return [
            [
                [ ],
                [ 'name', 'str' ]
            ],
            [
                [ ],
                [ 'name', 'string' ]
            ],
            [
                [ ],
                [ 'name', 'num' ]
            ],
            [
                [ ],
                [ 'name', 'numeric' ]
            ],
            [
                [ ],
                [ 'name', 'number' ]
            ],
            [
                [ ],
                [ 'name', 'int' ]
            ],
            [
                [ ],
                [ 'name', 'integer' ]
            ],
            [
                [ ],
                [ 'name', 'alphanumeric' ]
            ],
            [
                [ ],
                [ 'name', 'float' ]
            ],
            [
                [ ],
                [ 'name', 'array' ]
            ],
            [
                [ ],
                [ 'name', 'json' ]
            ],
            [
                [ ],
                [ 'name', 'null' ]
            ],
            [
                [ ],
                [ 'name', 'notNull' ]
            ],
            [
                [ ],
                [ 'name', 'bool' ]
            ],
            [
                [ ],
                [ 'name', 'boolean' ]
            ],
            [
                [ ],
                [ 'name', 'date' ]
            ],
            [
                [ ],
                [ 'name', 'datetime' ]
            ],
            [
                [ ],
                [ 'name', 'email' ]
            ],
            [
                [ ],
                [ 'name', 'email', 'domain' => 'qq.com' ]
            ],
            [
                [ ],
                [ 'name', 'url' ]
            ],
            [
                [ ],
                [ 'name', 'ip' ]
            ],
            [
                [ ],
                [ 'name', 'mobile' ]
            ],
            [
                [ ],
                [ 'name', 'phone' ]
            ],
            [
                [ ],
                [ 'name', 'between', 'min' => 0, 'max' => 128 ]
            ],
            [
                [ ],
                [ 'name', 'in', 'range' => [ 'foo', 'bar' ] ]
            ],
            [
                [ ],
                [ 'name', 'notIn', 'range' => [ 'foo', 'bar' ] ]
            ],
            [
                [ ],
                [ 'name', 'len', 'is' => 3 ]
            ],
            [
                [ ],
                [ 'name', 'length', 'is' => 2 ]
            ],
            [
                [ ],
                [ 'name', 'length', 'min' => 2 ]
            ],
            [
                [ ],
                [ 'name', 'len', 'max' => 4 ]
            ],
            [
                [ ],
                [ 'name', 'length', 'min' => 2, 'max' => 4 ]
            ],
            [
                [ ],
                [ 'name', 'greater', 'than' => 2 ]
            ],
            [
                [ ],
                [ 'name', 'less', 'than' => 10 ]
            ],
            [
                [ ],
                [ 'name', 'equals', 'to' => 10 ]
            ],
            [
                [ ],
                [ 'name', 'notEquals', 'to' => 100 ]
            ],
            [
                [ ],
                [ 'name', 'instance', 'of' => Validator::class ]
            ],
            [
                [ ],
                [ 'name', 'regex', 'pattern' => '/^[A-Za-z]+$/' ]
            ],
        ];
    }

    public function testOnScenarios()
    {
        $this->validator->addRules([
            [ 'name', 'required', 'on' => 'create' ],
            [ 'age', 'int', 'on' => ['create', 'update'] ],
            [ 'sex', 'str', 'except' => 'delete' ],
            [ 'salary', 'int' ]
        ]);

        $data = [
            'name' => 'foo',
            'age'  => 20,
            'sex'  => 'male'
        ];

        $this->validator->setScenario('create');
        $this->assertTrue($this->validator->validate($data));

        unset($data['name']);

        $this->validator->setScenario('update');
        $this->assertTrue($this->validator->validate($data));

        $this->validator->setScenario('delete');
        $this->assertTrue($this->validator->validate($data));

        $data['sex'] = 1;
        $this->assertFalse($this->validator->validate($data));
    }
}
