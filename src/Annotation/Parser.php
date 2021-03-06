<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Annotation
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Annotation;

use InvalidArgumentException;
use One\FileSystem\Finder;
use One\Support\Collection;
use Minime\Annotations\Reader;

/**
 * PHPDoc 解析类
 *
 * 此类实现有 `ArrayAccess`、`IteratorAggregate` 接口，可直接迭代使用，并实现了 `One\Support\Contracts\Collectable` 接口，
 * 可以使用 `all`， `get`, `set`, `has` 等方法操作数据集，具体操作方法可以详见 `One\Support\Collection` 类。
 *
 * @package     One\Annotation
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */
class Parser extends Collection
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
     * 是否已解析
     *
     * @var bool
     */
    private $parsed = false;

    /**
     * 构造
     *
     * @param string    $path
     * @param string    $interface
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $path, string $interface = 'One\\Context\\Contracts\\Action')
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
        if ($this->parsed === false) {
            foreach ($this->files as $file) {
                $className = $this->files->getClassName($file);
                $annotation = $this->reader->getClassAnnotations($className)->toArray();

                if (! empty($annotation)) {
                    $this->set($className, $annotation);
                }

                unset($className, $annotation);
            }

            $this->parsed = true;
        }
    }
}
