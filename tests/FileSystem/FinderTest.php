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

use One\FileSystem\Finder;

class FinderTest extends \PHPUnit\Framework\TestCase
{
    protected $finder;

    public function setUp()
    {
        $this->finder = new Finder(__DIR__ . '/Fixtures');
        $this->finder->setRootPath(ROOT_PATH);
        $this->finder->setAppPath(ROOT_PATH);
    }

    public function tearDown()
    {
        $this->finder = null;
    }

    public function testGetRootPath()
    {
        $this->assertEquals(ROOT_PATH, $this->finder->getRootPath());
    }

    public function testSetRootPath()
    {
        $this->finder->setRootPath('/tmp');
        $this->assertEquals('/tmp', $this->finder->getRootPath());
    }

    public function testGetAppPath()
    {
        $this->assertEquals(ROOT_PATH, $this->finder->getAppPath());
    }

    public function testSetAppPath()
    {
        $this->finder->setAppPath('/tmp');
        $this->assertEquals('/tmp', $this->finder->getAppPath());
    }

    public function testGetRootNamespace()
    {
        $finder = new Finder(ROOT_PATH . '/Fixtures');
        $finder->setRootPath(ROOT_PATH . '/Fixtures');

        $this->assertEquals('', $finder->getRootNamespace());
    }

    /**
     * @dataProvider provideWithMethods
     */
    public function testWithMethods($method, $param, $result)
    {
        $this->assertCount(
            $result,
            $this->finder->withExtension('php')->$method($param)->toArray(),
            $method
        );
    }

    public function provideWithMethods()
    {
        return [
            [ 'withSkip', 'abstract', 2 ],
            [ 'withSkip', 'interface', 2 ],
            [ 'withSkip', 'trait', 2 ],
            [ 'withSkipAll', '', 0 ],
            [ 'withNotSkipAll', '', 3 ],
            [ 'withPath', RUNTIME_PATH, 0 ],
            [ 'withPath', __DIR__ . '/Fixtures', 3 ],
            [ 'withInterface', 'ArrayAccess', 0 ],
            [ 'withExtension', 'php', 3 ],
        ];
    }

    public function testWithoutExtension()
    {
        $this->assertCount(3, $this->finder->toArray());
    }

    public function testWithInterface()
    {
        $this->assertCount(
            1,
            $this->finder
                ->withInterface('One\\Tests\\FileSystem\\Fixtures\\InterfaceClass')
                ->withInterface('One\\Tests\\FileSystem\\Fixtures\\InterfaceClass')
                ->toArray()
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPathException()
    {
        $this->finder->setRootPath('path/to/file');
    }
}
