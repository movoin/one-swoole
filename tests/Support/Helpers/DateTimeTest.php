<?php
/**
 * This file is part of the One package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     One\Tests\Support\Helpers
 * @author      Allen Luo <movoin@gmail.com>
 * @since       0.1
 */

namespace One\Tests\Support\Helpers;

use One\Support\Helpers\DateTime;

class DateTimeTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory()
    {
        $this->assertInstanceOf('\DateTime', DateTime::factory());

        $dateTime = new \DateTime('2018-07-03');
        $timeZone = new \DateTimeZone('UTC');
        $this->assertEquals($dateTime->setTimezone($timeZone), DateTime::factory($dateTime, 'UTC'));
    }

    public function testTimestamp()
    {
        $this->assertEquals(time(), DateTime::timestamp(time()));

        $dateTime = new \DateTime('2018-07-03 11:11:11');
        $this->assertEquals($dateTime->format('U'), DateTime::timestamp($dateTime));
    }

    public function testNow()
    {
        $dateTime = new \DateTime('@' . time());
        $dateTime->setTimezone(new \DateTimeZone('UTC'));
        $this->assertEquals($dateTime->format('Y/m/d H:i:s'), DateTime::now('Y/m/d H:i:s', 'UTC'));
    }
}
