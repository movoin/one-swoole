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

use RuntimeException;
use One\Context\Action;
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
class TestAction extends Action
{
    /**
     * 响应动作请求
     *
     * @param  \One\Protocol\Contracts\Request  $request
     *
     * @return \One\Context\Contracts\Payload
     */
    protected function run(Request $request): Payload
    {
        if ((int) $request->get('exception', 0) === 1) {
            $this->bad->dosomething();
        }

        return new \One\Context\Payload(
            200,
            'OK',
            '',
            [
                'hello' => 'world'
            ]
        );
    }
}
