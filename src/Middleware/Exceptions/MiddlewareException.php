<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Middleware\Exceptions
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Middleware\Exceptions;

class MiddlewareException extends \RuntimeException
{
    /**
     * 中间件类名
     *
     * @var string
     */
    protected $middleware;

    /**
     * 构造异常
     *
     * @param string          $message
     * @param string          $middleware
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct(string $message, string $middleware, int $code = 0, \Exception $previous = null)
    {
        $this->setMiddleware($middleware);
        parent::__construct(sprintf($message, $middleware), $code, $previous);
    }

    /**
     * 获得中间件类名
     *
     * @return string
     */
    public function getMiddleware(): string
    {
        return $this->middleware;
    }

    /**
     * 设置中间件类名
     *
     * @param  string $middleware
     */
    public function setMiddleware(string $middleware)
    {
        $this->middleware = $middleware;
    }
}
