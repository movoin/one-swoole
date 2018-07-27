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

use InvalidArgumentException;
use One\FileSystem\Finder;
use Minime\Annotations\Reader;

class Annotation extends Collection
{
    /**
     * 注释读取对象
     *
     * @var \Minime\Annotations\Reader
     */
    private $reader;
    /**
     * 文件搜索对象
     *
     * @var \One\FileSystem\Finder
     */
    private $files;

    /**
     * 构造
     *
     * @param string      $path
     * @param string|null $interface
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $path, string $interface = null)
    {
        if (! is_dir($path)) {
            throw new InvalidArgumentException('Directory ' . $path . ' not exists');
        }

        $this->reader = Reader::createFromDefaults();
        $this->files = (new Finder($path, $interface, 'php'))->withSkipAll();
    }

    /**
     * 解析文件注释
     */
    public function parse()
    {
        foreach ($this->files as $file) {
            $className = $this->finder->getClassName(parent::current());
            $annotation = $this->reader->getClassAnnotations($className);

            $this->set($className, $annotation);

            unset($className, $annotation);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function current(): array
    {
        $className = $this->finder->getClassName(parent::current());
        $annotation = $this->reader->getClassAnnotations($className);

        return [
            'class' => $className,
            'comments' => $annotation->toArray()
        ];
    }
}
