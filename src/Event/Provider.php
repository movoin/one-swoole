<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Event
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Event;

use One\Swoole\Provider as AbstractProvider;

class Provider extends AbstractProvider
{
    /**
     * 注册服务
     */
    public function register()
    {
        $this->bind('event', function ($server) {
            return new Emitter;
        });
    }
}
