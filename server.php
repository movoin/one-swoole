<?php

require __DIR__ . '/vendor/autoload.php';

/**
 * 运行环境
 */
define('RUN_MODE', 'local');
/**
 * 根目录
 */
define('ROOT_PATH', __DIR__);
/**
 * 应用根目录
 */
define('APP_PATH', ROOT_PATH . '/src');
/**
 * 配置文件目录
 */
define('CONFIG_PATH', ROOT_PATH . '/example/config');
/**
 * 运行时文件目录
 */
define('RUNTIME_PATH', ROOT_PATH . '/example/runtime');
