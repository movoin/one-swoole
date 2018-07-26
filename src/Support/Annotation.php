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

class Annotation
{
    /**
     * 注释结果
     *
     * @var array
     */
    protected $results = [];

    /**
     * 文件搜索对象
     *
     * @var \One\FileSystem\Finder
     */
    private $finder;

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

        $this->finder = new Finder($path, $interface, 'php');
    }
}
