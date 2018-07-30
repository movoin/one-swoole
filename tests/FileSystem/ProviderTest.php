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

use One\Config;
use One\Tests\Fixtures\ProviderTester;

class ProviderTest extends ProviderTester
{
    protected $target = 'One\\FileSystem\\Provider';

    public function testRegister()
    {
        $this->assertTrue($this->getServer()->has('fs'));
    }

    public function testInstance()
    {
        $this->assertInstanceOf(
            'One\\FileSystem\\Manager',
            $this->getServer()->get('fs')
        );

        @rmdir(RUNTIME_PATH . '/local');
    }
}
