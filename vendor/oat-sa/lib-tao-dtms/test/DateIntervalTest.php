<?php

namespace oat\dtms\Test;

use oat\dtms\DateInterval;

class DateIntervalTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function providerIntervalSpec()
    {

    }

    /**
     * @covers DateInterval::__construct
     * @param $intervalSpec Interval specification
     */
    public function testConstruct()
    {
        // normal DateInterval
        $interval = new DateInterval('PT2S');
        $this->assertInstanceOf('oat\\dtms\\DateInterval', $interval);
        $this->assertEquals('PT2S', $interval->format('PT%sS'));

        // with microseconds
        $interval = new DateInterval('PT2.2S');
        $this->assertInstanceOf('oat\\dtms\\DateInterval', $interval);
        $this->assertEquals('PT2.200000S', $interval->format('PT%sS'));
    }
}
