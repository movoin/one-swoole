<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Message
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Message;

use InvalidArgumentException;
use One\Protocol\Factory;
use One\Protocol\Contracts\Response as ResponseInterface;
use One\Protocol\Traits\HasMessage;
use One\Protocol\Traits\HasProtocol;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    use HasMessage,
        HasProtocol;
}
