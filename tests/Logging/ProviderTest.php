<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Logging
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Logging;

use One\Tests\Fixtures\ProviderTester;

class ProviderTest extends ProviderTester
{
    protected $target = 'One\\Logging\\Provider';

    public function testRegister()
    {
        $this->assertTrue($this->getServer()->has('logger'));
    }

    public function testInstance()
    {
        $this->assertInstanceOf(
            'One\\Logging\\Logger',
            $this->getServer()->get('logger')
        );
    }
}
