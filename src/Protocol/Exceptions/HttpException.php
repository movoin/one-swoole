<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Exceptions
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Exceptions;

use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;

class HttpException extends ProtocolException
{
    /**
     * 获得异常响应
     *
     * @param  \One\Protocol\Contracts\Request  $request
     * @param  \One\Protocol\Contracts\Response $response
     *
     * @return \One\Protocol\Contracts\Response
     */
    public function withResponse(Request $request, Response $response): Response
    {
    }
}
