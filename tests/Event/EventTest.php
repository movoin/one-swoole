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

use One\Event\Event;
use One\Event\Emitter;
use One\Event\Context;

class EventTest extends \PHPUnit\Framework\TestCase
{
    protected $event;

    public function setUp()
    {
        $this->event = new Event('event');
    }

    public function tearDown()
    {
        $this->event = null;
    }

    public function testGetName()
    {
        $this->assertEquals('event', $this->event->getName());
        $this->assertEquals('event', $this->event->name);
    }

    public function testGetContext()
    {
        $this->assertInstanceOf('One\Event\Context', $this->event->getContext());
    }

    public function testSetContext()
    {
        $this->event->setContexts(['foo' => 'bar']);
        $context = $this->event->getContext();

        $this->assertEquals('bar', $context->get('foo'));
    }

    public function testEmitter()
    {
        $this->event->setEmitter(new Emitter);
        $this->assertInstanceOf('One\Event\Emitter', $this->event->getEmitter());
    }
}
