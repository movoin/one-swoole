<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\FileSystem\Exceptions
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\FileSystem\Exceptions;

use One\FileSystem\FileSystem;
use One\FileSystem\Adapters\Local;
use One\Tests\FileSystem\ManagerTestCase;

class ManagerExceptionsTest extends ManagerTestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetFileSystemException()
    {
        $this->getManager()->getFileSystem('bad');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMountFileSystemException()
    {
        $this->getManager()->mountFileSystem('', new FileSystem(
            new Local(RUNTIME_PATH)
        ));
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testBadMethodCallException()
    {
        $this->getManager()->badMethod('local://bad');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFilterPrefixException1()
    {
        $this->getManager()->badMethod();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFilterPrefixException2()
    {
        $this->getManager()->badMethod('');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetPrefixAndPathException()
    {
        $this->getManager()->badMethod('bad');
    }
}
