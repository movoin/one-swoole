<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Event
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Event;

use One\Event\Listener;

class ListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetHandlerException()
    {
        $listener = new Listener('string');
    }
}
