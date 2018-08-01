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

use One\Event\Context;

class ContextTest extends \PHPUnit\Framework\TestCase
{
    public function testAll()
    {
        $context = new Context;
        $context->set('foo', 'bar');

        $this->assertEquals($context->toArray(), ['foo' => 'bar']);
        $this->assertNull($context->get('null'));
    }
}
