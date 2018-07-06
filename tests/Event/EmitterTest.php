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
use One\Tests\Event\Listeners\None;
use One\Tests\Event\Listeners\NoneOnce;

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

        if (file_exists('/tmp/test.lock')) {
            unlink('/tmp/test.lock');
        }
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

        $this->emitter->on(new Event('none'), None::class);
        $this->assertTrue($this->emitter->hasListener('none'));

        $this->emitter->once(new Event('none_once'), NoneOnce::class);
        $this->assertTrue($this->emitter->hasListener('none_once'));

        $this->emitter->on(new Event('none_class'), new None);
        $this->assertTrue($this->emitter->hasListener('none_class'));

        $this->emitter->once(new Event('none_once_class'), new NoneOnce);
        $this->assertTrue($this->emitter->hasListener('none_once_class'));
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
        $this->emitter->emit(new Event('array_once'));

        if (file_exists('/tmp/test.lock')) {
            unlink('/tmp/test.lock');
        }

        $this->emitter->on('array', [$this, 'handler']);
        $this->emitter->emit(new Event('array'));

        $this->assertFileExists('/tmp/test.lock');

        $this->emitter->emit('array_once');

        $this->assertFalse($this->emitter->hasListener('array_once'));
    }

    public function testGetListeners()
    {
        $this->assertEmpty($this->emitter->getListeners('none'));
    }

    public function testRemoveListener()
    {
        $this->assertEmpty($this->emitter->removeListener('none', ''));
        $this->emitter->on('array', [$this, 'handler']);
        $this->emitter->removeListener('array', [$this, 'handler']);
        $this->assertEmpty($this->emitter->removeListener('array', [$this, 'handler']));
        $this->emitter->on('array', new None);
        $this->emitter->removeListener('array', new None);
        $this->assertEmpty($this->emitter->removeListener('array', new None));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testException1()
    {
        $this->emitter->addListener(111, '');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testException2()
    {
        $this->emitter->addListener('111', 111);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testException3()
    {
        $this->emitter->addListener('111', 111, 0, true);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testException4()
    {
        $this->emitter->emit(111);
    }

    public function handler(Event $event)
    {
        file_put_contents('/tmp/test.lock', 'data');
    }
}
