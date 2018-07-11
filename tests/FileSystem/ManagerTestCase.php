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

class ManagerTestCase extends \PHPUnit\Framework\TestCase
{
    protected $manager;

    public function tearDown()
    {
        $this->manager = null;

        if (file_exists(RUNTIME_PATH . '/test.txt')) {
            unlink(RUNTIME_PATH . '/test.txt');
        }
    }

    protected function getManager()
    {
        if ($this->manager === null) {
            $this->manager = new Manager([
                'local' => new FileSystem(
                    new Local(RUNTIME_PATH)
                )
            ]);
        }

        return $this->manager;
    }
}
