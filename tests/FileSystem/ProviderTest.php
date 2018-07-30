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
    public function testRegister()
    {
        $provider = $this->provider('One\\FileSystem\\Provider');

        $provider->register();

        $this->assertTrue($this->getServer()->has('fs'));
    }
}
