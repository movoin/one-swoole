<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests;

use One\Run;

class RunTest extends \PHPUnit\Framework\TestCase
{
    public function testGetName()
    {
        $this->assertEquals('one', Run::name());
    }

    public function testGetVersion()
    {
        $this->assertTrue(is_string(Run::version()));
    }

    public function testGetMode()
    {
        $this->assertEquals(Run::TEST, Run::mode());
    }
}
