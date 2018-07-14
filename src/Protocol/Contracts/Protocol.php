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

use One\Net\Contracts\Request;
use One\Net\Contracts\Response;

interface Protocol
{
    /**
     * 协议处理
     *
     * @param  \One\Net\Contracts\Request   $request
     * @param  \One\Net\Contracts\Response  $response
     */
    public function handle(Request $request, Response $response);
}
