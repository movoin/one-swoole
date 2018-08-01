<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Protocol\Message
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Protocol\Message;

use One\Protocol\Message\Cookies;

class CookiesTest extends \PHPUnit\Framework\TestCase
{
    protected $cookies;

    public function setUp()
    {
        $this->cookies = new Cookies([
            'foo' => 'bar'
        ]);
    }

    public function tearDown()
    {
        $this->cookies = null;
    }

    public function testSetDefaults()
    {
        $this->cookies->setDefaults(['domain' => '.foobar.com']);
        $this->cookies->set('foo', 'tar');

        $this->assertEquals(
            [
                [
                    'key' => 'foo',
                    'value' => 'tar',
                    'domain' => '.foobar.com',
                    'path' => '/',
                    'expires' => 0,
                    'secure' => false,
                    'httponly' => false
                ]
            ],
            $this->cookies->toArray()
        );
    }

    public function testGet()
    {
        $this->assertEquals('bar', $this->cookies->get('foo'));
        $this->assertEquals(null, $this->cookies->get('bad'));
    }

    public function testSet()
    {
        $this->cookies->set('foo', [
            'value' => 'bar',
            'domain' => '.domain.com',
            'path' => '/',
            'expires' => '2018-08-20 23:59:59',
            'secure' => true,
            'httponly' => true,
        ]);

        $this->assertEquals(
            [
                [
                    'key' => 'foo',
                    'value' => 'bar',
                    'domain' => '.domain.com',
                    'path' => '/',
                    'expires' => strtotime('2018-08-20 23:59:59'),
                    'secure' => true,
                    'httponly' => true
                ]
            ],
            $this->cookies->toArray()
        );
    }
}
