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

use One\Support\Traits\HasSingleton;

class HasSingletonTest extends \PHPUnit\Framework\TestCase
{
    public function testHasSingleton()
    {
        $this->assertInstanceOf(MyTest::class, MyTest::singleton());
    }
}

class MyTest
{
    use HasSingleton;
}
