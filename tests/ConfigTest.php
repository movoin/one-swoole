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

use One\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadConfig()
    {
        Config::load();
        Config::load();

        $this->assertEquals('One', Config::get('name'));
    }

    public function testClearConfig()
    {
        Config::clear();

        $this->assertNull(Config::get('name'));
    }

    public function testWriteConfig()
    {
        $this->assertTrue(Config::writeFile('test.yml', [ ['foo' => 'bar'] ]));

        unlink(CONFIG_PATH . '/test.yml');
    }

    public function testUndefinedRuntimePath()
    {
        Config::clear();
        Config::setRootPath(CONFIG_PATH . '2');
        Config::addPlaceHolder('{ROOT_PATH}', ROOT_PATH);

        Config::load(true);
        $this->assertEquals('/tmp', Config::get('server.http.runtime_path'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetRootPathException()
    {
        Config::setRootPath('not/found/path');
    }
}
