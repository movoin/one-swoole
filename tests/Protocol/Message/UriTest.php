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

use One\Protocol\Message\Uri;

class UriTest extends \PHPUnit\Framework\TestCase
{
    protected $uri;

    public function setUp()
    {
        $this->uri = new Uri('http://user:pass@domain.com:9051/path/to/file?filter=on#end');
    }

    public function tearDown()
    {
        $this->uri = null;
    }

    public function testToString()
    {
        $this->assertEquals(
            'http://user:pass@domain.com:9051/path/to/file?filter=on#end',
            (string) $this->uri
        );

        $uri = $this->uri->withPath('path/to/file');

        $this->assertEquals(
            'http://user:pass@domain.com:9051/path/to/file?filter=on#end',
            (string) $uri
        );
    }

    /**
     * @dataProvider provideGetMethods
     */
    public function testGetUri($method, $result)
    {
        $methodName = 'get' . ucfirst($method);

        $this->assertEquals($this->uri->$methodName(), $result, "{$methodName} failed");
    }

    public function provideGetMethods()
    {
        return [
            [ 'scheme',     'http' ],
            [ 'authority',  'user:pass@domain.com:9051' ],
            [ 'userInfo',   'user:pass' ],
            [ 'host',       'domain.com' ],
            [ 'port',       9051  ],
            [ 'path',       '/path/to/file' ],
            [ 'query',      'filter=on' ],
            [ 'fragment',   'end' ]
        ];
    }

    /**
     * @dataProvider provideWithMethods
     */
    public function testWithUri($method, $attributes, $result)
    {
        $withMethod = 'with' . ucfirst($method);
        $getMethod = 'get' . ucfirst($method);

        $uri = call_user_func_array([$this->uri, $withMethod], $attributes);

        $this->assertEquals($uri->$getMethod(), $result);
    }

    public function provideWithMethods()
    {
        return [
            [ 'scheme',     [ 'http' ],             'http' ],
            [ 'scheme',     [ 'https' ],            'https' ],
            [ 'userInfo',   [ 'user', 'pass' ],     'user:pass' ],
            [ 'userInfo',   [ 'hello', 'world' ],   'hello:world' ],
            [ 'host',       [ 'domain.com' ],       'domain.com' ],
            [ 'host',       [ 'domain.net' ],       'domain.net' ],
            [ 'port',       [ 9051 ],               9051 ],
            [ 'port',       [ 8080 ],               8080 ],
            [ 'path',       [ '/path/to/file' ],    '/path/to/file' ],
            [ 'path',       [ '/foo/bar' ],         '/foo/bar' ],
            [ 'query',      [ 'filter=on' ],        'filter=on' ],
            [ 'query',      [ 'filter=off' ],       'filter=off' ],
            [ 'fragment',   [ 'end' ],              'end' ],
            [ 'fragment',   [ 'start' ],            'start' ]
        ];
    }

    public function testGetAuthority()
    {
        $uri = new Uri('/path/to/file');

        $this->assertEquals('', $uri->getAuthority());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructException()
    {
        $uri = new Uri('http:///bad');
    }

    /**
     * @dataProvider provideFilterExceptions
     * @expectedException \InvalidArgumentException
     */
    public function testFilterExceptions($method, $attributes)
    {
        $withMethod = 'with' . ucfirst($method);
        call_user_func_array([$this->uri, $withMethod], $attributes);
    }

    public function provideFilterExceptions()
    {
        return [
            [ 'scheme',     [ 1024 ] ],
            [ 'scheme',     [ 'bad' ] ],
            [ 'host',       [ '' ] ],
            [ 'port',       [ 'foo' ] ],
            [ 'port',       [ 100000 ] ],
            [ 'path',       [ 1024 ] ],
            [ 'query',      [ 1024 ] ],
            [ 'fragment',   [ 1024 ] ]
        ];
    }

    public function testFilterQuery()
    {
        $uri = $this->uri->withQuery('foo=bar @+%/');
        $this->assertEquals('foo=bar%20@+%25/', $uri->getQuery());
    }

    public function testFilterFragment()
    {
        $uri = $this->uri->withFragment('bar @+%/');
        $this->assertEquals('bar%20@+%25/', $uri->getFragment());
    }
}
