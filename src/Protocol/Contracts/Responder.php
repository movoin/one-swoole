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

use One\Context\Contracts\Payload;
use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;

interface Responder
{
    /**
     * 响应请求
     *
     * @param  \One\Protocol\Contracts\Request  $request
     * @param  \One\Protocol\Contracts\Response $response
     * @param  \One\Context\Contracts\Payload   $payload
     *
     * @return \One\Protocol\Contracts\Response
     */
    public function __invoke(Request $request, Response $response, Payload $payload): Response;
}
