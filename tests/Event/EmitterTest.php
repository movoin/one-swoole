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
use One\Event\Listener;

class EmitterTest extends \PHPUnit\Framework\TestCase
{
    protected $emitter;

    public function setUp()
    {
        $this->emitter = new Emitter;
    }

    public function tearDown()
    {
        $this->emitter = null;
    }

    public function testOn()
    {
        $this->emitter->on('array', [$this, 'handler']);
        $this->emitter->on(new Event('class'), function (Event $event) {});
        $this->emitter->once('array_once', [$this, 'handler']);
        $this->emitter->once(new Event('once'), function (Event $event) {});

        $this->assertTrue($this->emitter->hasListener('array'));
        $this->assertTrue($this->emitter->hasListener('class'));
        $this->assertTrue($this->emitter->hasListener('array_once'));
        $this->assertTrue($this->emitter->hasListener('once'));
    }

    public function testOff()
    {
        $this->emitter->on('array', [$this, 'handler']);
        $this->emitter->off('array');

        $this->assertFalse($this->emitter->hasListener('array'));
    }

    public function testEmit()
    {
        $this->emitter->once('array_once', [$this, 'handler']);
        $this->emitter->emit('array_once');

        $this->assertFalse($this->emitter->hasListener('array_once'));
    }

    public function handler(Event $event)
    {
    }
}
