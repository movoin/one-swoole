<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Annotation\Fixtures
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Annotation\Fixtures;

use One\Context\Contracts\Action;
use One\Context\Contracts\Payload;
use One\Protocol\Contracts\Request;

/**
 * Hello Action
 *
 * @methods GET
 */
class HelloAction implements Action
{
    /**
     * 响应动作请求
     *
     * @param  \One\Protocol\Contracts\Request  $request
     *
     * @return \One\Context\Contracts\Payload
     */
    public function __invoke(Request $request): Payload
    {
    }
}
