<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\FileSystem
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\FileSystem;

class ContentListingFormatter
{
    /**
     * 目录名称
     *
     * @var string
     */
    private $directory;
    /**
     * @var bool
     */
    private $recursive;

    /**
     * 构造
     *
     * @param string $directory
     * @param bool   $recursive
     */
    public function __construct(string $directory, bool $recursive)
    {
        $this->directory = $directory;
        $this->recursive = $recursive;
    }

    /**
     * 格式化列表
     *
     * @param array $listing
     *
     * @return array
     */
    public function formatListing(array $listing): array
    {
        $listing = array_values(
            array_map(
                [$this, 'addPathInfo'],
                array_filter($listing, [$this, 'isEntryOutOfScope'])
            )
        );

        return $this->sortListing($listing);
    }

    /**
     * 添加路径信息
     *
     * @param array $entry
     *
     * @return array
     */
    private function addPathInfo(array $entry): array
    {
        return $entry + $this->pathinfo($entry['path']);
    }

    /**
     * 返回是否超出范围
     *
     * @param array $entry
     *
     * @return bool
     */
    private function isEntryOutOfScope(array $entry): bool
    {
        if (empty($entry['path']) && $entry['path'] !== '0') {
            return false;
        }

        if ($this->recursive) {
            return $this->residesInDirectory($entry);
        }

        return $this->isDirectChild($entry);
    }

    /**
     * 返回是否在父目录中
     *
     * @param $entry
     *
     * @return bool
     */
    private function residesInDirectory(array $entry): bool
    {
        if ($this->directory === '') {
            return true;
        }

        return strpos($entry['path'], $this->directory . '/') === 0;
    }

    /**
     * 返回是否为目录的直接子项
     *
     * @param $entry
     *
     * @return bool
     */
    private function isDirectChild(array $entry): bool
    {
        return $this->dirname($entry['path']) === $this->directory;
    }

    /**
     * 返回排序的列表
     *
     * @param array $listing
     *
     * @return array
     */
    private function sortListing(array $listing): array
    {
        usort(
            $listing,
            function ($a, $b) {
                return strcasecmp($a['path'], $b['path']);
            }
        );

        return $listing;
    }

    /**
     * 标准化路径信息
     *
     * @param string $path
     *
     * @return array
     */
    private function pathinfo(string $path): array
    {
        $pathinfo = pathinfo($path) + compact('path');
        $pathinfo['dirname'] = array_key_exists('dirname', $pathinfo) ?
                            $this->normalizeDirname($pathinfo['dirname']) :
                            '';

        return $pathinfo;
    }

    /**
     * 标准化目录名
     *
     * @param  string $dirname
     *
     * @return string
     */
    private function normalizeDirname(string $dirname): string
    {
        return $dirname === '.' ? '' : $dirname;
    }

    /**
     * 获得标准目录名
     *
     * @param  string $path
     *
     * @return string
     */
    private function dirname(string $path): string
    {
        return $this->normalizeDirname(dirname($path));
    }
}
