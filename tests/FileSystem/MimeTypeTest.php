<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\FileSystem
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\FileSystem;

use One\FileSystem\MimeType;

class MimeTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testDetectByFileExtension()
    {
        $this->assertEquals('text/plain', MimeType::detectByFileExtension('txt'));
        $this->assertEquals('text/plain', MimeType::detectByFileExtension('bad'));
    }

    public function testDetectByFilePath()
    {
        $this->assertEquals('text/plain', MimeType::detectByFilePath('txt'));
        $this->assertEquals('application/octet-stream', MimeType::detectByFilePath('foo.bin'));
    }
}
