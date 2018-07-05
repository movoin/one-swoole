<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

require __DIR__ . '/../vendor/autoload.php';

/**
 * 运行环境
 */
define('RUN_MODE', 'test');
/**
 * 根目录
 */
define('ROOT_PATH', __DIR__ . '/../example');
/**
 * 应用根目录
 */
define('APP_PATH', ROOT_PATH . '/app');
/**
 * 配置文件目录
 */
define('CONFIG_PATH', __DIR__ . '/One/config');
/**
 * 运行时文件目录
 */
define('RUNTIME_PATH', ROOT_PATH . '/runtime');
