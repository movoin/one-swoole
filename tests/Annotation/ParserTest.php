<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Annotation
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Annotation;

use One\Annotation\Parser;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    protected $parser;

    public function setUp()
    {
        $this->parser = new Parser(__DIR__ . '/Fixtures');
    }

    public function tearDown()
    {
        $this->parser = null;
    }

    public function testParse()
    {
        $this->parser->parse();

        $this->assertEquals(1, $this->parser->count());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPathException()
    {
        new Parser('path/to/file');
    }
}
