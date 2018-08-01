<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Protocol\Providers
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Protocol\Providers;

use One\Swoole\Provider;

class Environment extends Provider
{
    /**
     * 初始化环境
     */
    public function boot()
    {
        // 默认时区
        date_default_timezone_get('Asia/Shanghai');
        // 字符编码
        mb_internal_encoding('UTF-8');
        // 设置运行内存限制
        ini_set('memory_limit', '200M');
        // 重置 OPCache
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
}
