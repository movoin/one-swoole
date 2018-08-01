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

class AdapterTestCase extends \PHPUnit\Framework\TestCase
{
    protected $adapter;

    public function setUp()
    {
        $this->writeNewFile();
    }

    public function tearDown()
    {
        $this->adapter = null;

        if (file_exists(RUNTIME_PATH . '/test.txt')) {
            unlink(RUNTIME_PATH . '/test.txt');
        }
    }

    protected function getAdapter()
    {
        if ($this->adapter === null) {
            $this->adapter = new Local(RUNTIME_PATH);
        }

        return $this->adapter;
    }

    protected function writeNewFile()
    {
        $this->getAdapter()->write('test.txt', 'test');
    }
}
