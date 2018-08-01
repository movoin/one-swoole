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

class ExceptionHandler extends Provider
{
    /**
     * 初始化环境
     */
    public function boot()
    {
        register_shutdown_function(function () {
            $error = error_get_last();
            $trace = debug_backtrace();

            if (isset($error['type'])) {
                switch ($error['type']) {
                    case E_ERROR:
                    case E_PARSE:
                    case E_CORE_ERROR:
                    case E_COMPILE_ERROR:
                    case E_USER_ERROR:
                        // {{ log
                        $this->log('error', '核心错误', [
                            'error' => $error,
                            'trace' => $trace
                        ]);
                        // }}
                        break;

                    default:
                        break;
                }
            }

            unset($error, $trace);
        });
    }
}
