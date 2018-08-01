<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Logging
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Logging;

use One\Logging\Logger;

class LoggerTest extends \PHPUnit\Framework\TestCase
{
    protected $logger;

    public function setUp()
    {
        $this->logger = new Logger(
            'test',
            RUNTIME_PATH . '/test.log'
        );
    }

    public function tearDown()
    {
        $this->logger = null;

        if (file_exists(RUNTIME_PATH . '/test.log')) {
            unlink(RUNTIME_PATH . '/test.log');
        }
    }

    public function testGetLogger()
    {
        $this->assertInstanceOf('Monolog\\Logger', $this->logger->getLogger());
    }

    public function testLog()
    {
        $this->logger->log('debug', 'Debug test');
        $this->logger->info('Info test');

        $this->assertFileExists(RUNTIME_PATH . '/test.log');
    }
}
