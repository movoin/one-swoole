<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Fixtures\Middlewares
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Fixtures\Middlewares;

use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;
use One\Middleware\Contracts\Interceptor;

class OneInterceptor implements Interceptor
{
    /**
     * 拦截请求，返回拦截后的请求和响应
     *
     * @param  \One\Protocol\Contracts\Request  $request
     * @param  \One\Protocol\Contracts\Response $response
     *
     * @return [\One\Protocol\Contracts\Request, \One\Protocol\Contracts\Response]
     */
    public function doIntercept(Request $request, Response $response): array
    {
        return [
            $request,
            $response
        ];
    }
}
