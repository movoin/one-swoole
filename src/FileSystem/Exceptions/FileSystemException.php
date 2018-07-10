<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\FileSystem\Exceptions
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\FileSystem\Exceptions;

class FileSystemException extends \Exception
{
    /**
     * 文件路径
     *
     * @var string
     */
    private $path;

    /**
     * 获得文件路径
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * 设置文件路径
     *
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }
}
