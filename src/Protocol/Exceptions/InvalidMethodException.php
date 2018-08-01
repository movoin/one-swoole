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

class InvalidMethodException extends \InvalidArgumentException
{
    /**
     * 请求对象
     *
     * @var \One\Protocol\Contracts\Request
     */
    protected $request;

    /**
     * 构造
     *
     * @param \One\Protocol\Contracts\Request   $request
     * @param string                            $method
     */
    public function __construct(Request $request, string $method)
    {
        $this->request = $request;
        parent::__construct(sprintf('Unsupported HTTP method "%s" provided', $method));
    }

    /**
     * 获得请求对象
     *
     * @return \One\Protocol\Contracts\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
