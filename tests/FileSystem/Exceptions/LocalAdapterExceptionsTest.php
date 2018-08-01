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

use One\Tests\FileSystem\AdapterTestCase;

class LocalAdapterExceptionsTest extends AdapterTestCase
{
    /**
     * @expectedException One\FileSystem\Exceptions\DirectoryExistsException
     */
    public function testCreateDirException()
    {
        $this->getAdapter()->createDir('test3');
        $this->getAdapter()->createDir('test3');
    }

    /**
     * @expectedException One\FileSystem\Exceptions\DirectoryNotExistsException
     */
    public function testDeleteDirException()
    {
        $this->getAdapter()->deleteDir('test3');
        $this->getAdapter()->deleteDir('test3');
    }
}
