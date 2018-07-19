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
            'foo' => [
                'value' => 'bar',
                'domain' => '.domain.com',
                'hostonly' => null,
                'path' => '/',
                'expires' => '2018-08-20 23:59:59',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]
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
                'foo=tar; domain=.foobar.com'
            ],
            $this->cookies->toHeaders()
        );
    }

    public function testGet()
    {
        $this->assertEquals(
            [
                'value' => 'bar',
                'domain' => '.domain.com',
                'hostonly' => null,
                'path' => '/',
                'expires' => '2018-08-20 23:59:59',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ],
            $this->cookies->get('foo')
        );

        $this->assertEquals(null, $this->cookies->get('bad'));
    }

    public function testSet()
    {
        $this->cookies->set('foo', [
            'value' => 'bar',
            'domain' => '.domain.com',
            'hostonly' => null,
            'path' => '/',
            'expires' => '2018-08-20 23:59:59',
            'secure' => true,
            'hostonly' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        $this->assertEquals(
            [
                'foo=bar; domain=.domain.com; path=/; expires='
                . gmdate('D, d-M-Y H:i:s e', strtotime('2018-08-20 23:59:59'))
                . '; secure; Hostonly; Httponly; SameSite=Strict'
            ],
            $this->cookies->toHeaders()
        );
    }
}
