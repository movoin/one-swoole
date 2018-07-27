<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Middleware
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Middleware;

use One\Context\Contracts\Action;
use One\Middleware\Contracts\Filter;
use One\Middleware\Contracts\Interceptor;
use One\Middleware\Contracts\Terminator;
use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;
use One\Protocol\Traits\HasServer;

class Manager
{
    use HasServer;

    /**
     * 匹配请求的中间件
     *
     * @var array
     */
    protected $matched = [];

    /**
     * 构造
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * 重置匹配中间件
     */
    public function reset()
    {
        $this->matched = [];
    }
}
