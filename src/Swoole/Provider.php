<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Swoole
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Swoole;

use One\Config\Config;
use One\Swoole\Contracts\Server;
use One\Swoole\Contracts\Provider as ProviderInterface;

abstract class Provider implements ProviderInterface
{
    /**
     * Server 实例
     *
     * @var \One\Swoole\Contracts\Server
     */
    private $server;

    /**
     * 构造
     *
     * @param \One\Swoole\Contracts\Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * 注册服务
     */
    public function register()
    {
    }

    /**
     * 启动服务
     */
    public function boot()
    {
    }

    /**
     * 绑定对象
     *
     * @param  string   $abstract
     * @param  mixed    $concrete
     */
    final protected function bind(string $abstract, $concrete = null)
    {
        $this->server->bind($abstract, $concrete);
    }

    /**
     * 映射别名
     *
     * @param  string   $abstract
     * @param  string   $alias
     */
    final protected function alias(string $abstract, string $alias)
    {
        $this->server->alias($abstract, $alias);
    }

    /**
     * 绑定对象
     *
     * @param  string   $abstract
     * @param  mixed    $default
     *
     * @return mixed
     */
    final protected function config(string $key, $default = null)
    {
        return Config::get($key, $default);
    }

    /**
     * 写入日志
     *
     * @param  string   $level
     * @param  string   $message
     * @param  array    $context
     */
    final protected function log(string $level, string $message, array $context = [])
    {
        $this->provider('logger')->log($level, $message, $context);
    }

    /**
     * 获得容器中的指定实例
     *
     * @param  string   $name
     *
     * @return mixed
     */
    final protected function provider(string $name)
    {
        return $this->server->get($name);
    }
}
