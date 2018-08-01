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

use One\Middleware\Contracts\Filter;
use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;

class AcmeFilter implements Filter
{
    /**
     * 过滤请求，返回 null 则放行请求
     *
     * @param  \One\Protocol\Contracts\Request  $request
     * @param  \One\Protocol\Contracts\Response $response
     *
     * @return \One\Protocol\Contracts\Response|null
     */
    public function doFilter(Request $request, Response $response)
    {
        if ((int) $request->get('error') === 1) {
            return
                $response
                    ->withStatusCode(404)
                    ->write('error')
            ;
        }
    }
}
