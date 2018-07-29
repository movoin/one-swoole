<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Responders\Handlers
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Responders\Handlers;

use One\Context\Contracts\Payload;
use One\Protocol\Contracts\RespondHandler;

class JsonHandler implements RespondHandler
{
    /**
     * 接受类型
     *
     * @return array
     */
    public function accepts(): array
    {
        return [
            'application/json',
            'application/vnd.api+json',
            'application/vnd.geo+json'
        ];
    }

    /**
     * 内容类型
     *
     * @return string
     */
    public function type(): string
    {
        return 'application/json';
    }

    /**
     * 内容处理
     *
     * @param  \One\Context\Contracts\Payload $payload
     *
     * @return string
     */
    public function body(Payload $payload): string
    {
        return $payload->toJson();
    }
}
