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

use One\Support\Container;
use One\Swoole\Traits\HasConfig;
use One\Swoole\Traits\HasSwoole;
use One\Protocol\Traits\HasProtocol;
use One\Swoole\Contracts\Server as ServerInterface;

abstract class Server extends Container implements ServerInterface
{
    use HasConfig,
        HasSwoole,
        HasProtocol;

    /**
     * 默认监听 IP
     */
    const DEFAULT_HOST = '0.0.0.0';
    /**
     * 默认监听端口
     */
    const DEFAULT_PORT = 9501;

    /**
     * 服务名称
     *
     * @var string
     */
    protected $serverName;
    /**
     * 进程名称
     *
     * @var string
     */
    protected $processName;

    /**
     * 构造
     *
     * @param string $serverName
     * @param string $processName
     *
     * @throws \InvalidArgumentException
     * @throws \One\Swoole\Exceptions\SwooleException
     */
    public function __construct(string $serverName, string $processName)
    {
        $this->serverName = trim(strtolower($serverName));
        $this->processName = trim(strtolower($processName));

        $this->initializeConfig();
    }

    /**
     * 获得完整服务名称
     *
     * @return string
     */
    public function getName(): string
    {
        return sprintf('%s-%s', $this->serverName, $this->processName);
    }

    /**
     * 获得进程名称
     *
     * @return string
     */
    public function getProcessName(): string
    {
        return $this->processName;
    }

    /**
     * 获得 SOCK 连接
     *
     * @return string
     */
    public function getSock(): string
    {
        return $this->getConfig('sockFile', '');
    }

    /**
     * 判断服务是否处于运行状态
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        $pid = $this->getPid();
        return $pid && posix_kill($pid, 0);
    }

    /**
     * 设置进程名称
     *
     * @param string $name
     */
    protected function setProcessName(string $name)
    {
        swoole_set_process_name($this->getName() . ': ' . $name);
    }

    /**
     * 设置运行用户
     */
    protected function setRunUser()
    {
        if (($username = $this->getConfig('swoole.user')) !== null) {
            $user = posix_getpwnam($username);

            if ($user) {
                posix_setuid($user['uid']);
                posix_setgid($user['gid']);
            }

            unset($user, $username);
        }
    }

    /**
     * 获得主进程 PID
     *
     * @return int
     */
    protected function getPid(): int
    {
        $pid = 0;

        if (is_readable($this->getConfig('pidFile'))) {
            $pid = file_get_contents($this->getConfig('pidFile'));
        }

        return $pid;
    }
}
