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

use One\Protocol\Message\Headers;

class HeadersTest extends \PHPUnit\Framework\TestCase
{
    protected $header;

    public function setUp()
    {
        $this->header = new Headers;
    }

    public function tearDown()
    {
        $this->header = null;
    }

    public function testAll()
    {
        $this->setDefaultHeaders();

        $headers = [
            'Accept'            => ['text/html'],
            'Accept-Encoding'   => ['gzip, deflate'],
            'Accept-Language'   => ['zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7'],
            'Cache-Control'     => ['no-cache'],
            'Cookie'            => ['PHPSESSID=web'],
            'DNT'               => ['1'],
            'Oragma'            => ['no-cache'],
            'Http-User-Agent'   => ['Firefox/5.0'],
        ];

        $this->assertEquals($headers, $this->header->all());
    }

    public function testGet()
    {
        $this->setDefaultHeaders();

        $this->assertEquals(['text/html'], $this->header->get('accept'));
        $this->assertEquals(null, $this->header->get('bad'));
    }

    public function testGetOriginalKey()
    {
        $this->setDefaultHeaders();

        $this->assertEquals('Accept', $this->header->getOriginalKey('accept'));
        $this->assertEquals(null, $this->header->getOriginalKey('bad'));
    }

    public function testHas()
    {
        $this->setDefaultHeaders();

        $this->assertTrue($this->header->has('accept'));
    }

    public function testAdd()
    {
        $this->header->add('foo', 'bar');

        $this->assertEquals(['bar'], $this->header->get('foo'));
    }

    public function testRemove()
    {
        $this->setDefaultHeaders();

        $this->header->remove('accept');
        $this->assertFalse($this->header->has('accept'));
    }

    protected function setDefaultHeaders()
    {
        $headers = [
            'Accept'            => 'text/html',
            'Accept-Encoding'   => 'gzip, deflate',
            'Accept-Language'   => 'zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control'     => 'no-cache',
            'Cookie'            => 'PHPSESSID=web',
            'DNT'               => '1',
            'Oragma'            => 'no-cache',
            'Http-User-Agent'   => 'Firefox/5.0',
        ];

        foreach ($headers as $key => $value) {
            $this->header->set($key, $value);
        }
    }
}
