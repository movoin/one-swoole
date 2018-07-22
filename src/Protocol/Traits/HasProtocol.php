<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Traits;

use One\Protocol\Contracts\Protocol;

trait HasProtocol
{
    /**
     * 协议
     *
     * @var \One\Protocol\Contracts\Protocol
     */
    protected $protocol;

    /**
     * 获得协议
     *
     * @return \One\Protocol\Contracts\Protocol
     */
    public function getProtocol(): Protocol
    {
        return $this->protocol;
    }

    /**
     * 设置协议
     *
     * @param  \One\Protocol\Contracts\Protocol $protocol
     */
    public function setProtocol(Protocol $protocol)
    {
        $this->protocol = $protocol;
    }
}
