<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Support\Helpers
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Support\Helpers;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

final class Yaml
{
    /**
     * Symfony YAML 解析器
     *
     * @var \Symfony\Component\Yaml\Parser
     */
    private static $parser;
    /**
     * Symfony YAML 转换器
     *
     * @var \Symfony\Component\Yaml\Dumper
     */
    private static $dumper;

    /**
     * 解析 YAML 文件
     *
     * @param  string $filename
     * @param  mixed  $default
     *
     * @return mixed
     */
    public static function parseFile(string $filename, $default = null)
    {
        if (file_exists($filename)) {
            return static::getParser()->parseFile($filename);
        }

        return $default;
    }

    /**
     * 解析 YAML 内容
     *
     * @param  string $input
     *
     * @return mixed
     */
    public static function parse(string $input)
    {
        return static::getParser()->parse($input);
    }

    /**
     * 转换为 YAML
     *
     * @param  mixed $input
     *
     * @return string
     */
    public static function dump($input): string
    {
        return static::getDumper()->dump($input);
    }

    /**
     * 获得 YAML 解析器
     *
     * @return \Symfony\Component\Yaml\Parser
     */
    public static function getParser()
    {
        if (static::$parser === null) {
            static::$parser = new Parser;
        }

        return static::$parser;
    }

    /**
     * 获得 YAML 转换器
     *
     * @return \Symfony\Component\Yaml\Dumper
     */
    public static function getDumper()
    {
        if (static::$dumper === null) {
            static::$dumper = new Dumper;
        }

        return static::$dumper;
    }
}
