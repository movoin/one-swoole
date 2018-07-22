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

class Response extends \Swoole\Http\Response
{
    public static function newResponse()
    {
        return new static();
    }

    public function header($key, $value, $ucwords = null)
    {
        return true;
    }

    public function status($http_code)
    {
        return true;
    }

    public function end($content = null)
    {
        return true;
    }
}
