<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Contracts
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Contracts;

use Psr\Http\Message\ResponseInterface;

interface Response extends ResponseInterface
{
    /**
     * 获得请求协议
     *
     * @return string
     */
    public function getProtocol(): string;

    /**
     * 设置请求协议
     *
     * @param  string $protocol
     *
     * @return self
     */
    public function withProtocol(string $protocol);
}
