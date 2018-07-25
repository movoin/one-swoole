<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol;

use One\Protocol\Contracts\Protocol as ProtocolInterface;
use One\Protocol\Contracts\Request;
use One\Protocol\Contracts\Response;
use One\Protocol\Traits\HasServer;
use One\Protocol\Traits\HasStartItem;
use One\Protocol\Traits\HasMiddleware;

class Protocol implements ProtocolInterface
{
    use HasServer,
        HasStartItem,
        HasMiddleware;
}
