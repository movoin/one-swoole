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

use One\FileSystem\Manager;
use One\FileSystem\FileSystem;
use One\FileSystem\Adapters\Local;

class ManagerTest extends ManagerTestCase
{
    public function testGetFileSystem()
    {
        $this->assertInstanceOf(
            'One\\FileSystem\\FileSystem',
            $this->getManager()->getFileSystem('local')
        );
    }

    public function testWrite()
    {
        $this->getManager()->write('local://test.txt', 'test');
        $this->assertTrue($this->getManager()->exists('local://test.txt'));
    }
}
