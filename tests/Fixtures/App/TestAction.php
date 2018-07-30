<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Fixtures\App
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Fixtures\App;

use One\Context\Contracts\Action;
use One\Context\Contracts\Payload;
use One\Protocol\Contracts\Request;

/**
 * 测试用例
 *
 * @summary TestCase
 * @description TestCase
 *
 * @method GET
 * @route /test
 *
 * @rules {
 *        "name": {
 *            "description": "Say hello to Username",
 *            "type": "string",
 *            "required": true
 *        }
 * }
 */
class TestAction implements Action
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
        return new One\Context\Payload();
    }
}
