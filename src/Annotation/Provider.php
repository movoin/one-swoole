<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Annotation
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Annotation;

use One\Swoole\Provider as AbstractProvider;

class Provider extends AbstractProvider
{
    /**
     * 注册服务
     */
    public function register()
    {
        $this->bind('annotation', function ($server) {
            return new Parser(APP_PATH);
        });
    }

    /**
     * 启动服务
     */
    public function boot()
    {
        $this->provider('annotation')->parse();
    }
}
