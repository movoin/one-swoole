<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Protocol\Message\Swoole
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Protocol\Message\Swoole;

class Request extends \Swoole\Http\Request
{
    public static function createGetMethodRequest()
    {
        $request = new static();

        $request->fd = 1;
        $request->header = Factory::newHeaders();
        $request->server = Factory::newServer();
        $request->cookie = Factory::newCookie();
        $request->get = Factory::newGet();

        return $request;
    }

    public static function createPostMethodRequest()
    {
        $request = new static();

        $request->fd = 1;
        $request->header = Factory::newHeaders();
        $request->server = Factory::newServer('POST');
        $request->cookie = Factory::newCookie();
        $request->post = Factory::newFormPost();

        return $request;
    }

    public static function createDeleteMethodRequest()
    {
        $static->method = 'POST';
    }

    public static function createFileUploadRequest()
    {
        $static->method = 'FILE';
    }

    public function rawContent()
    {
        return '';
    }
}
