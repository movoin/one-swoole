<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Console
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Console;

use Exception;
use DirectoryIterator;
use ReflectionClass;
use One\Config;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{
    /**
     * LOGO
     *
     * @var string
     */
    private static $logo = ' __________________
 _  __ \_  __ \  _ \
 / /_/ /  / / /  __/
 \____//_/ /_/\___/

';
    /**
     * @var string
     */
    private $stripeVersion = '';

    /**
     * 构造
     *
     * @param string $name
     * @param string $version
     */
    public function __construct($name = Config::NAME, $version = Config::VERSION)
    {
        $this->checkRunEnv();

        Config::load();

        // {{
        parent::__construct(
            Config::get('name', $name),
            Config::get('version', $version)
        );
        // }}

        $this->stripeVersion = Config::get('stripe_version', '');

        // 核心命令
        $this->addCommandInPath(dirname(__DIR__));
        // 自定义命令
        $this->addCommandInPath(APP_PATH);
    }

    /**
     * {@inheritDoc}
     */
    public function getHelp()
    {
        return static::$logo . parent::getHelp();
    }

    /**
     * {@inheritDoc}
     */
    public function getLongVersion()
    {
        return sprintf(
            '<info>%s</info> version <comment>%s</comment> %s',
            ucfirst($this->getName()),
            $this->getVersion(),
            ! empty($this->stripeVersion) ? $this->stripeVersion : 'dev'
        );
    }

    /**
     * 检测运行环境
     */
    protected function checkRunEnv()
    {
        if (! defined('ROOT_PATH')) {
            throw new Exception('"ROOT_PATH" is not defined');
        }

        if (! defined('APP_PATH')) {
            throw new Exception('"APP_PATH" is not defined');
        }

        if (! defined('CONFIG_PATH')) {
            throw new Exception('"CONFIG_PATH" is not defined');
        }

        if (! defined('RUNTIME_PATH')) {
            throw new Exception('"RUNTIME_PATH" is not defined');
        }
    }

    /**
     * 从路径中添加命令
     *
     * @param string $root
     */
    protected function addCommandInPath(string $root)
    {
        $iterator = new DirectoryIterator($root);

        foreach ($iterator as $path) {
            if ($path->isDot()) {
                continue;
            }

            if ($path->isDir()) {
                $this->addCommandInPath($path->getPathName());
            }

            if ($path->isFile() && $path->getExtension() === 'php') {
                if ($command = $this->createCommand($path)) {
                    $this->add($command);
                }
            }
        }

        unset($iterator);
    }

    /**
     * 创建命令对象
     *
     * @param  \DirectoryIterator $path
     *
     * @return false|\Symfony\Component\Console\Command\Command
     */
    protected function createCommand(DirectoryIterator $path)
    {
        if (! ($namespace = $this->lookupNamespace($path->getPathName()))) {
            return false;
        }

        $command = new ReflectionClass($namespace . '\\' . $path->getBasename('.php'));

        if ($command->isAbstract() || $command->isInterface() || $command->isTrait()) {
            return false;
        }

        if (! $command->implementsInterface('\\One\\Console\\Contracts\\Command')) {
            return false;
        }

        return $command->newInstance();
    }

    /**
     * 解析类命名空间
     *
     * @param  string $path
     *
     * @return string|false
     */
    private function lookupNamespace(string $path)
    {
        $content = file_get_contents($path);

        preg_match(
            '#' .
            preg_quote('namespace') .
            '(.+?)' .
            preg_quote(';') .
            '#s',
            $content,
            $matchs
        );

        unset($content);

        return isset($matchs[1]) ? trim($matchs[1]): false;
    }
}
