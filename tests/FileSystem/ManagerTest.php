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

    public function testCopy()
    {
        $this->assertTrue($this->getManager()->copy('local://word.docx', 'local://test.docx'));
        $this->assertFileExists(RUNTIME_PATH . '/test.docx');
        $this->getManager()->delete('local://test.docx');
    }

    public function testMove()
    {
        $this->assertTrue($this->getManager()->copy('local://word.docx', 'local://test.docx'));
        $this->assertTrue($this->getManager()->move(
            'local://test.docx',
            'local://test2.docx',
            ['visibility' => 'private']
        ));
        $this->assertFileExists(RUNTIME_PATH . '/test2.docx');
        $this->getManager()->delete('local://test2.docx');
    }

    public function testMoveTo()
    {
        $this->getManager()->mountFileSystem('test', new FileSystem(
            new Local(RUNTIME_PATH)
        ));

        $this->assertTrue($this->getManager()->copy('local://word.docx', 'local://test.docx'));
        $this->assertTrue($this->getManager()->move(
            'local://test.docx',
            'test://test2.docx',
            ['visibility' => 'private']
        ));


        $this->assertFileExists(RUNTIME_PATH . '/test2.docx');
        $this->getManager()->delete('test://test2.docx');
    }

    public function testListContents()
    {
        $list = $this->getManager()->listContents('local://folder', true);

        $this->assertEquals(count($list), 3);
    }
}
