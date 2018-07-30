<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Protocol\Protocols
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Protocol\Protocols;

use One\Protocol\Message\Response;

class FakeResponse extends Response
{
    /**
     * 发送响应
     */
    public function end()
    {
        return $this;
    }

    /**
     * 设置 Gzip 压缩
     *
     * @param int $level
     *
     * @return void
     */
    public function setGzip(int $level = 1)
    {
    }
}
