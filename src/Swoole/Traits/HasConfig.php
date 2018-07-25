<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole\Traits
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole\Traits;

use One\Config;
use One\Support\Helpers\Arr;
use One\Swoole\Exceptions\SwooleException;

trait HasConfig
{
    /**
     * 配置信息
     *
     * @var array
     */
    protected $config = [];

    /**
     * 准备配置信息
     *
     * @throws \InvalidArgumentException
     * @throws \One\Swoole\Exceptions\SwooleException
     */
    protected function prepareConfig()
    {
        if (! defined('CONFIG_PATH')) {
            throw new SwooleException('Undefined constant `CONFIG_PATH`');
        }

        // 加载配置
        Config::load();
    }

    /**
     * 初始化配置
     *
     * @throws \InvalidArgumentException
     * @throws \One\Swoole\Exceptions\SwooleException
     */
    protected function initializeConfig()
    {
        // {{ 准备配置信息
        $this->prepareConfig();
        // }}

        // 初始化配置
        $config = Config::get('server.' . $this->getProcessName(), []);

        if (! isset($config['protocol'])) {
            throw new SwooleException('The server protocol is undefined');
        }

        if (isset($config['listen'])) {
            $uri = parse_url($config['listen']);
        } else {
            $uri = [];
        }

        $config['host'] = isset($uri['host']) ? $uri['host'] : self::DEFAULT_HOST;
        $config['port'] = isset($uri['port']) ? $uri['port'] : self::DEFAULT_PORT;
        unset($uri);

        $this->config = $config;

        unset($config);
    }

    /**
     * 获得指定配置内容
     *
     * @param  string $name
     * @param  mixed  $default
     *
     * @return mixed
     */
    protected function getConfig(string $name, $default = null)
    {
        return Arr::get($this->config, $name, $default);
    }
}
