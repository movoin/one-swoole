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

interface Protocol
{
    /**
     * 协议类型
     */
    const HTTP      = 'http';
    const TCP       = 'tcp';
    const UDP       = 'udp';
    const WEBSOCKET = 'websocket';

    /**
     * 协议处理
     *
     * @param  \One\Protocol\Contracts\Request   $request
     * @param  \One\Protocol\Contracts\Response  $response
     */
    public function handle(Request $request, Response $response);
}
