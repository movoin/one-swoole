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

use DirectoryIterator;
use InvalidArgumentException;
use One\Support\Helpers\Arr;
use One\Support\Helpers\Assert;
use One\Support\Helpers\Yaml;

final class Config
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

    const NAME     = 'One';
    const VERSION  = '0.1';

    /**
     * 配置
     *
     * @var array
     */
    private static $config = [];
    /**
     * 占位符
     *
     * @var array
     */
    private static $placeholders = [];
    /**
     * 配置文件根目录
     *
     * @var string
     */
    private static $root;
    /**
     * 配置文件模式目录
     *
     * @var string
     */
    private static $path;

    /**
     * 返回运行模式
     *
     * @return string
     */
    public static function mode(): string
    {
        return defined('RUN_MODE') ? RUN_MODE : static::DEPLOY;
    }

    /**
     * 设置配置文件根目录
     *
     * @param string $root
     *
     * @throws \InvalidArgumentException
     */
    public static function setRootPath(string $root)
    {
        $mode = static::mode();
        $path = "{$root}/{$mode}";

        if (! file_exists($path)) {
            throw new InvalidArgumentException("'{$path}' not exists");
        }

        static::$root = realpath($root);
        static::$path = realpath($path);

        unset($mode, $path);
    }

    /**
     * 获得指定配置内容
     *
     * @param  string $name
     * @param  mixed  $default
     *
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        return Arr::get(static::$config, $name, $default);
    }

    /**
     * 清空配置
     */
    public static function clear()
    {
        static::$config = [];
    }

    /**
     * 加载配置
     *
     * @param  bool $force
     *
     * @throws \RuntimeException
     */
    public static function load($force = false)
    {
        if ($force === false && ! empty(static::$config)) {
            return;
        }

        if (! isset(static::$placeholders['{ROOT_PATH}'])) {
            static::addPlaceHolder('{ROOT_PATH}', ROOT_PATH);
        }

        if (defined('RUNTIME_PATH') &&
            ! isset(static::$placeholders['{RUNTIME_PATH}'])
        ) {
            static::addPlaceHolder('{RUNTIME_PATH}', RUNTIME_PATH);
        }

        if (defined('CONFIG_PATH') && ! isset(static::$root)) {
            static::setRootPath(CONFIG_PATH);
        }

        $conf = static::importFromPath(static::$path);

        // 全局配置
        $globals = [
            'swoole' => static::$root . '/swoole.yml',
            'global' => static::$root . '/global.yml',
        ];

        foreach ($globals as $name => $path) {
            if (file_exists($path)) {
                $conf[$name] = static::readFile($path);
            }
        }

        unset($globals);

        // 主配置
        if (! isset($conf['global']['name'])) {
            $conf['global']['name'] = static::NAME;
        }

        if (! isset($conf['global']['version'])) {
            $conf['global']['version'] = static::VERSION;
        }

        // 协议配置
        if (isset($conf['server'])) {
            foreach ($conf['server'] as $name => &$server) {
                // Swoole 配置
                if (isset($server['swoole'])) {
                    $server['swoole'] = array_merge(
                        $conf['swoole'],
                        $server['swoole']
                    );
                } else {
                    $server['swoole'] = $conf['swoole'];
                }

                // Protocol
                $server['protocol'] = isset($server['protocol']) ?
                                    strtolower($server['protocol']) :
                                    'http';

                // Runtime
                if (! isset($server['runtime_path'])) {
                    $server['runtime_path'] = isset($conf['global']['runtime_path']) ?
                                            $conf['global']['runtime_path'] :
                                            '/tmp';
                }

                // Swoole 日志
                if (! isset($server['swoole']['log_file'])) {
                    $server['swoole']['log_file'] = $server['runtime_path'] . '/logs/error.log';
                }

                // TCP 监听
                if (! isset($server['sock'])) {
                    $server['sock'] = $server['runtime_path'] . '/var/' . $name . '.sock';
                }

                // 主进程 PID 文件位置
                $server['swoole']['pid_file'] = $server['runtime_path'] . '/var/' . $name . '.pid';
            }
        }

        $global = $conf['global'];

        unset($conf['swoole'], $conf['global']);

        // {{
        static::$config = static::preformPlaceHolder(array_merge($global, $conf));
        // }}

        unset($conf, $global);
    }

    /**
     * 添加占位符
     *
     * @param string $placeholder
     * @param string $path
     */
    public static function addPlaceHolder(string $placeholder, string $path)
    {
        static::$placeholders[$placeholder] = trim($path);
    }

    /**
     * 读取文件
     *
     * @param  string $filename
     * @param  mixed  $default
     *
     * @return array|null
     */
    public static function readFile(string $filename, $default = null)
    {
        return Yaml::parseFile($filename, $default);
    }

    /**
     * 写入配置文件
     *
     * @param  string $filename
     * @param  array  $config
     *
     * @return bool
     */
    public static function writeFile(string $filename, array $config): bool
    {
        return (bool) file_put_contents(
            static::$root . '/' . $filename,
            Yaml::dump($config)
        );
    }

    /**
     * 从路径导入配置文件
     *
     * @param  string $path
     *
     * @return array
     */
    private static function importFromPath(string $path): array
    {
        $conf = [];
        $paths = new DirectoryIterator($path);

        foreach ($paths as $path) {
            if ($path->isDot()) {
                continue;
            }

            if ($path->isDir()) {
                $conf[$path->getFileName()] = static::importFromPath($path->getPathName());
            }

            if ($path->isFile()) {
                if ($path->getExtension() === 'yml') {
                    $pathinfo = pathinfo($path->getFileName());

                    $conf[$pathinfo['filename']] = (array) static::readFile($path->getPathName(), []);

                    unset($pathinfo);
                }
            }
        }

        unset($paths);

        return $conf;
    }

    /**
     * 预处理占位符
     *
     * @param  array $config
     *
     * @return array
     */
    private static function preformPlaceHolder(array $config): array
    {
        $preform = [];

        foreach ($config as $name => $value) {
            if (Assert::array($value)) {
                $preform[$name] = static::preformPlaceHolder($value);
            } elseif (Assert::string($value)) {
                $preform[$name] = trim($value);

                foreach (static::$placeholders as $placeholder => $path) {
                    if (Assert::contains($preform[$name], $placeholder)) {
                        $preform[$name] = str_replace($placeholder, $path, $preform[$name]);
                    }
                }
            } else {
                $preform[$name] = trim($value);
            }
        }

        return $preform;
    }
}
