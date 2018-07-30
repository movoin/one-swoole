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

use RuntimeException;
use One\FileSystem\Contracts\Adapter;
use One\Support\Helpers\Reflection;
use One\Swoole\Provider as AbstractProvider;

class Provider extends AbstractProvider
{
    /**
     * 注册服务
     */
    public function register()
    {
        if (($config = $this->config('filesystem')) === null) {
            throw new RuntimeException('The FileSystem is undefined');
        }

        $this->bind('fs', function ($server) use ($config) {
            $fss = [];

            foreach ($config as $prefix => $setting) {
                $fss[$prefix] = $this->createFileSystem($setting);
            }

            return new Manager($fss);
        });

        unset($config);
    }

    /**
     * 创建文件系统
     *
     * @param  array  $config
     *
     * @return \One\FileSystem\FileSystem
     * @throws \RuntimeException
     */
    protected function createFileSystem(array $config): FileSystem
    {
        if (! isset($config['adapter'])) {
            throw new RuntimeException('Filesystem adapter is undefined');
        }

        $adapters = [
            'local' => 'Local',
            'aliyun' => 'Aliyun',
        ];

        if (! isset($adapters[$config['adapter']])) {
            throw new RuntimeException(
                sprintf('Filesystem adapter "%s" is invalid', $config['adapter'])
            );
        }

        $adapter = $adapters[$config['adapter']];

        unset($adapters);

        return new FileSystem(
            $this->createAdapter($adapter, $config)
        );
    }

    /**
     * 创建适配器
     *
     * @param  string $adapter
     * @param  array  $config
     *
     * @return \One\FileSystem\Contracts\Adapter
     * @throws \RuntimeException
     */
    protected function createAdapter(string $adapter, array $config): Adapter
    {
        if ($adapter === 'Local') {
            if (! isset($config['config']['path'])) {
                throw new RuntimeException('Filesystem local adapter must set `path`');
            }

            $config = [$config['config']['path']];
        }

        return Reflection::newInstance(
            'One\\FileSystem\\Adapters\\' . $adapter,
            $config
        );
    }
}
