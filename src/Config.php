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
    private static $rootPath;

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

        static::$rootPath = realpath($path);

        unset($mode, $path);
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

        $conf = static::importFromPath(static::$rootPath);

        // 全局配置
        $globals = [
            'swoole' => static::$rootPath . '/swoole.yml',
            'global' => static::$rootPath . '/global.yml',
        ];

        foreach ($globals as $name => $path) {
            if (file_exists($path)) {
                $conf[$name] = static::readFile($path);
            }
        }

        unset($globals);

        // 主配置
        if (! isset($conf['main']['name'])) {
            $conf['main']['name'] = Run::name() . '-' . Run::mode();
        }

        // 协议配置
    }

    /**
     * 添加占位符
     *
     * @param string $placeholder
     * @param string $path
     */
    public static function addPlaceHolder(string $placeholder, string $path)
    {
        static::$placeholders[$placeholder] = $path;
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
            static::$rootPath . '/' . $filename,
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

                    $conf[$pathinfo['filename']] = static::preformPlaceHolder(
                        (array) static::readFile($path->getPathName(), [])
                    );

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
