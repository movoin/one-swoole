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

class FileExistsException extends FileSystemException
{
    /**
     * 构造
     *
     * @param string          $path
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct(string $path, int $code = 0, \Exception $previous = null)
    {
        parent::__construct(
            $path,
            'File "%s" already exist',
            $code,
            $previous
        );
    }
}
