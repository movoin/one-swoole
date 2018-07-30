<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Fixtures
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Fixtures;

use One\Config;
use One\Protocol\Server;

class ProviderTester extends \PHPUnit\Framework\TestCase
{
    protected $server;
    protected $provider;

    public function setUp()
    {
        Config::setRootPath(CONFIG_PATH);
        $this->server = new Server('test', 'http');
    }

    public function tearDown()
    {
        $this->server = null;
        $this->provider = null;
        Config::clear();
    }

    public function getServer()
    {
        return $this->server;
    }

    public function provider(string $name)
    {
        if ($this->provider === null) {
            $this->provider = $this->getServer()->resolve($name, [$this->getServer()]);
        }

        return $this->provider;
    }
}
