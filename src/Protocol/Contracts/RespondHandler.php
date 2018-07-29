<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Contracts
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Contracts;

use One\Context\Contracts\Payload;

interface RespondHandler
{
    /**
     * 接受类型
     *
     * @return array
     */
    public function accepts(): array;

    /**
     * 内容类型
     *
     * @return string
     */
    public function type(): string;

    /**
     * 内容处理
     *
     * @param  \One\Context\Contracts\Payload $payload
     *
     * @return string
     */
    public function body(Payload $payload): string;
}
