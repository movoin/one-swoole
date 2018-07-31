<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Example\Middlewares
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Example\Middlewares;

use One\Middleware\Contracts\Terminator;
use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;

class AcmeTerminator implements Terminator
{
    /**
     * 终止请求，请求结束后对 Response 对象进行重写
     *
     * @param  \One\Protocol\Contracts\Request  $request
     * @param  \One\Protocol\Contracts\Response $response
     *
     * @return \One\Protocol\Contracts\Response
     */
    public function doTerminate(Request $request, Response $response): Response
    {
        return $response->write('hello');
    }
}
