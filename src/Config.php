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

use InvalidArgumentException;
use DirectoryIterator;
use One\Run;
use One\Support\Helpers\Arr;
use One\Support\Helpers\Assert;
use One\Support\Helpers\Yaml;

final class Config
{
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
     * 设置配置文件根目录
     *
     * @param string $root
     *
     * @throws InvalidArgumentException
     */
    public static function setRootPath(string $root)
    {
        $mode = Run::mode();
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
     */
    public static function load($force = false)
    {
        if ($force === false && ! empty(static::$config)) {
            return;
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
            $conf['global']['name'] = Run::name() . '-' . Run::mode();
        }

        if (! isset($conf['global']['version'])) {
            $conf['global']['version'] = Run::version();
        }

        // 协议配置
        if (isset($conf['protocol'])) {
            foreach ($conf['protocol'] as $name => &$protocol) {
                // Swoole 配置
                if (isset($protocol['swoole'])) {
                    $protocol['swoole'] = array_merge(
                        $conf['swoole'],
                        $protocol['swoole']
                    );
                } else {
                    $protocol['swoole'] = $conf['swoole'];
                }

                // Protocol
                $protocol['protocol'] = isset($protocol['protocol']) ?
                                        strtolower($protocol['protocol']) :
                                        'http';

                // Runtime
                if (! isset($protocol['runtimePath'])) {
                    $protocol['runtimePath'] = isset($conf['global']['runtimePath']) ?
                                                $conf['global']['runtimePath'] :
                                                '/tmp';
                }

                // Swoole 日志
                if (! isset($protocol['swoole']['log_file'])) {
                    $protocol['swoole']['log_file'] = $protocol['runtimePath'] . '/logs/error.log';
                }

                // TCP 监听
                if (! isset($protocol['sock'])) {
                    $protocol['sock'] = $protocol['runtimePath'] . '/var/' . $name . '.sock';
                }

                // 主进程 PID 文件位置
                $protocol['swoole']['pid_file'] = $protocol['runtimePath'] . '/var/' . $name . '.pid';
            }
        }

        unset($conf['swoole']);

        // {{
        static::$config = static::preformPlaceHolder($conf);
        // }}

        unset($conf);
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
