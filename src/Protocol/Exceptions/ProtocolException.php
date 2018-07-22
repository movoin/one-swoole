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

class ProtocolException extends \RuntimeException
{
    /**
     * 协议
     *
     * @var string
     */
    protected $protocol;

    /**
     * 协议不支持
     *
     * @param  string $protocol
     *
     * @return self
     */
    public static function notSupport(string $protocol): self
    {
        return new static('Protocol "%s" does not support', $protocol);
    }

    /**
     * 构造异常
     *
     * @param string          $message
     * @param string          $protocol
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct(string $message, string $protocol, int $code = 0, \Exception $previous = null)
    {
        $this->setProtocol($protocol);
        parent::__construct(sprintf($message, $protocol), $code, $previous);
    }

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
}
