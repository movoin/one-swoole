<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One;

use One\Support\Helpers\Assert;

final class Run
{
    /**
     * 运行模式
     *
     * - DEPLOY : 部署
     * - DEV    : 开发
     * - TEST   : 测试
     * - LOCAL  : 本机
     */
    const DEPLOY   = 'deploy';
    const DEV      = 'devel';
    const TEST     = 'test';
    const LOCAL    = 'local';

    /**
     * 返回名称
     *
     * @return string
     */
    public static function name(): string
    {
        return 'one';
    }

    /**
     * 返回当前版本
     *
     * @return string
     */
    public static function version(): string
    {
        return '0.1';
    }

    /**
     * 返回运行模式
     *
     * @return string
     */
    public static function mode(): string
    {
        if ($mode = strtolower(get_cfg_var('one.run_mode')) && static::checkMode($mode)) {
            return $mode;
        }

        if (defined('RUN_MODE')) {
            return RUN_MODE;
        }

        return self::DEPLOY;
    }

    /**
     * 检查是否在许可范围
     *
     * @param  string $mode
     *
     * @return bool
     */
    private static function checkMode($mode): bool
    {
        return Assert::oneOf($mode, [
            self::DEPLOY,
            self::DEV,
            self::TEST,
            self::LOCAL
        ]);
    }
}
