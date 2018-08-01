<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Routing
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Routing;

use One\Annotation\Parser;
use One\Tests\Fixtures\ProviderTester;

class ProviderTest extends ProviderTester
{
    protected $target = 'One\\Routing\\Provider';

    public function testRegister()
    {
        $this->assertTrue($this->getServer()->has('router'));
    }

    public function testInstance()
    {
        $this->getServer()->bind('annotation', function ($server) {
            return new Parser(APP_PATH . '/Fixtures');
        });
        $this->getServer()->get('annotation')->parse();

        $this->assertInstanceOf(
            'One\\Routing\\Router',
            $this->getServer()->get('router')
        );
    }
}
