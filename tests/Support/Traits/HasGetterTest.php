<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Support\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Support\Traits;

use One\Support\Traits\HasGetter;

class HasGetterTest extends \PHPUnit\Framework\TestCase
{
    public function testHasGetter()
    {
        $mock = new MyGetter;

        $this->assertEquals('getter', $mock->getter);
        $this->assertEquals('parent', $mock->parent);
    }

    /**
     * @expectedException Exception
     */
    public function testException()
    {
        $mock = new MyGetter2;
        $mock->error;
    }
}

class MyParent
{
    public function __get($name)
    {
        if ($name === 'parent') {
            return $name;
        }
    }
}

class MyGetter extends MyParent
{
    use HasGetter;

    public function getGetter()
    {
        return 'getter';
    }
}

class MyGetter2
{
    use HasGetter;

    public function getGetter()
    {
        return 'getter';
    }
}
