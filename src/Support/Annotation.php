<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Support
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Support;

use CallbackFilterIterator;
use FilesystemIterator;
use InvalidArgumentException;
use Iterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use One\Support\Helpers\Arr;
use One\Support\Helpers\Json;
use Minime\Annotations\Reader;

class Annotation
{
    /**
     * 遍历根路径
     *
     * @var string
     */
    protected $path;
    /**
     * 匹配接口、父类
     *
     * @var string
     */
    protected $implements;
    /**
     * 注释结果
     *
     * @var array
     */
    protected $results = [];
    /**
     * 命名空间
     *
     * @var string
     */
    private $namespace;

    /**
     * 构造
     *
     * @param string      $path
     * @param string|null $implements
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $path, string $implements = null)
    {
        if (! is_dir($path)) {
            throw new InvalidArgumentException('Directory ' . $path . ' not exists');
        }

        $this->path = $path;
        $this->implements = $implements;
    }

    /**
     * 加载路径中的文件
     *
     * @return /Iterator
     */
    public function loadFiles(): Iterator
    {
        return new CallbackFilterIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            ), function ($current) {
                return $current->isFile() && $current->getExtension() == 'php';
            }
        );
    }

    /**
     * 获得应用命名空间
     *
     * @return string
     */
    public function getNamespace(): string
    {
        if (! is_null($this->namespace)) {
            return $this->namespace;
        }

        $composer = Json::decode(file_get_contents(ROOT_PATH . '/composer.json'));

        foreach ((array) Arr::get($composer, 'autoload.psr-4', []) as $namespace => $paths) {
            foreach ((array) $paths as $path) {
                if (realpath(APP_PATH) === realpath(ROOT_PATH . '/' . $path)) {
                    return $this->namespace = $namespace;
                }
            }
        }

        return '';
    }

    /**
     * 获得路径对应的命名空间
     *
     * @param  string $pathname
     *
     * @return string
     */
    public function getPathNamespace(string $pathname): string
    {
        return rtrim(
                $this->getNamespace(),
                '\\'
            ) .
            str_replace(
                '/',
                '\\',
                str_replace(
                    APP_PATH,
                    '',
                    $pathname
                )
            ) .
            '\\';
    }
}
