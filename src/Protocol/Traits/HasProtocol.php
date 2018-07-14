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

trait HasProtocol
{
    /**
     * 协议
     *
     * @var string
     */
    protected $protocol;

    /**
     * 获得协议
     *
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * 设置协议
     *
     * @param  string $protocol
     */
    public function setProtocol(string $protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * 设置协议
     *
     * @param  string $protocol
     *
     * @return self
     */
    public function withProtocol(string $protocol): self
    {
        $clone = clone $this;
        $clone->protocol = $protocol;

        return $clone;
    }
}
