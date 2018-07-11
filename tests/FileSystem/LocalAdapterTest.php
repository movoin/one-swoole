<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\FileSystem
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\FileSystem;

use One\FileSystem\Adapters\Local;

class LocalAdapterTest extends AdapterTestCase
{
    public function testExists()
    {
        $this->assertTrue($this->getAdapter()->exists('test.txt'));
    }

    public function testRead()
    {
        $this->assertEquals('test', $this->getAdapter()->read('test.txt'));
    }

    public function testReadStream()
    {
        $this->assertTrue(is_resource($this->getAdapter()->readStream('test.txt')));
    }
}
