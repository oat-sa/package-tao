<?php
/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoQtiTest\test\runner\time;

use oat\taoQtiTest\models\runner\time\QtiTimer;
use oat\taoQtiTest\models\runner\time\QtiTimeLine;
use oat\taoQtiTest\models\runner\time\QtiTimeStorage;
use oat\taoTests\models\runner\time\TimePoint;
use oat\tao\test\TaoPhpUnitTestRunner;
use ReflectionClass;

/**
 * Test the {@link \oat\taoQtiTest\models\runner\time\QtiTimer}
 *
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class QtiTimerTest extends TaoPhpUnitTestRunner
{
    /**
     * @throws \common_ext_ExtensionException
     */
    public function setUp()
    {
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
    }

    /**
     * Test the TimeLine instantiation
     */
    public function testConstructor()
    {
        $timer = new QtiTimer();
        $this->assertInstanceOf('oat\taoTests\models\runner\time\Timer', $timer);
        $timeLine = $this->getTimeLine($timer);
        $this->assertInstanceOf('oat\taoQtiTest\models\runner\time\QtiTimeLine', $timeLine);
    }

    /**
     * Test the QtiTimer::start()
     */
    public function testStart()
    {
        $timer = new QtiTimer();
        $timeLine = $this->getTimeLine($timer);
        $timePoints = $timeLine->getPoints();
        $this->assertTrue(empty($timePoints));
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];

        $timer->start($tags, 1459335000.0000);
        $timePoints = $timeLine->getPoints();
        $this->assertEquals(1, count($timePoints));
        $this->assertEquals(TimePoint::TARGET_SERVER, $timePoints[0]->getTarget());
        $this->assertEquals(TimePoint::TYPE_START, $timePoints[0]->getType());


        $timer->start($tags, 1459335002.0000);
        $timePoints = $timeLine->getPoints();
        $this->assertEquals(3, count($timePoints));
        //end time of the first range should be authomatically creted
        $this->assertEquals(1459335001.9999, $timePoints[1]->getTimeStamp());
        $this->assertEquals(TimePoint::TARGET_SERVER, $timePoints[1]->getTarget());
        $this->assertEquals(TimePoint::TYPE_END, $timePoints[1]->getType());

        $this->assertEquals(1459335002, $timePoints[2]->getTimeStamp());
        $this->assertEquals(TimePoint::TARGET_SERVER, $timePoints[2]->getTarget());
        $this->assertEquals(TimePoint::TYPE_START, $timePoints[2]->getType());
    }

    /**
     * @dataProvider startInvalidDataExceptionProvider
     * @param $routeItem
     * @param $timestamp
     * @expectedException \oat\taoTests\models\runner\time\InvalidDataException
     */
    public function testStartInvalidDataException($routeItem, $timestamp)
    {
        $timer = new QtiTimer();
        $timer->start($routeItem, $timestamp);
    }

    /**
     * @expectedException \oat\taoTests\models\runner\time\InconsistentRangeException
     */
    public function testStartInconsistentRangeException()
    {
        $timer = new QtiTimer();
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        $timer->start($tags, 1459335000.0000);
        $timer->start($tags, 1459334999.0000);
    }

    /**
     * Test the QtiTimer::end()
     */
    public function testEnd()
    {
        $timer = new QtiTimer();
        $timeLine = $this->getTimeLine($timer);
        $timePoints = $timeLine->getPoints();
        $this->assertTrue(empty($timePoints));
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        $timer->start($tags, 1459335000.0000);

        $timer->end($tags, 1459335010.0000);
        $timePoints = $timeLine->getPoints();

        $this->assertEquals(2, count($timePoints));
        $this->assertEquals(1459335010.0000, $timePoints[1]->getTimestamp());
        $this->assertEquals(TimePoint::TARGET_SERVER, $timePoints[1]->getTarget());
        $this->assertEquals(TimePoint::TYPE_END, $timePoints[1]->getType());
    }

    /**
     * @expectedException \oat\taoTests\models\runner\time\InconsistentRangeException
     */
    public function testEndInconsistentRangeException()
    {
        $timer = new QtiTimer();
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        $timer->start($tags, 1459335000.0000);
        $timer->end($tags, 1459335010.0000);
        $timer->end($tags, 1459335011.0000);
    }

    /**
     * @dataProvider endInvalidDataExceptionProvider
     * @param $tags
     * @param $timestamp
     * @expectedException \oat\taoTests\models\runner\time\InvalidDataException
     */
    public function testEndInvalidDataException($tags, $timestamp)
    {
        $timer = new QtiTimer();
        $timer->end($tags, $timestamp);
    }

    /**
     * Test the QtiTimer::adjust()
     * * @dataProvider adjustDataProvider
     */
    public function testAdjust($startTimestamp, $endTimestamp, $duration, $expectedDuration)
    {
        $timer = new QtiTimer();
        $timeLine = $this->getTimeLine($timer);
        $timePoints = $timeLine->getPoints();
        $this->assertTrue(empty($timePoints));
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        $timer->start($tags, $startTimestamp);
        $timer->end($tags, $endTimestamp);
        $timePoints = $timeLine->getPoints();

        $this->assertEquals(2, count($timePoints));
        $this->assertEquals([], $timeLine->find(null, TimePoint::TARGET_CLIENT));

        $timer->adjust($tags, $duration);
        $timePoints = $timeLine->getPoints();
        $clientTimePoints = $timeLine->find(null, TimePoint::TARGET_CLIENT);
        $this->assertEquals(4, count($timePoints));
        $this->assertEquals(2, count($clientTimePoints));
        $clientStartPoint = $clientTimePoints[0];
        $clientEndPoint = $clientTimePoints[1];

        $serverDuration = $endTimestamp - $startTimestamp;
        if (is_null($duration)) {
            $duration = $serverDuration;
        }
        $delay = ($serverDuration - $duration) / 2;

        $this->assertEquals($startTimestamp + $delay, $clientStartPoint->getTimestamp());
        $this->assertEquals(TimePoint::TARGET_CLIENT, $clientStartPoint->getTarget());
        $this->assertEquals(TimePoint::TYPE_START, $clientStartPoint->getType());

        $this->assertEquals($endTimestamp - $delay, $clientEndPoint->getTimestamp());
        $this->assertEquals(TimePoint::TARGET_CLIENT, $clientEndPoint->getTarget());
        $this->assertEquals(TimePoint::TYPE_END, $clientEndPoint->getType());

        $this->assertEquals($expectedDuration, $timer->compute(null, TimePoint::TARGET_CLIENT));
    }

    /**
     * @dataProvider adjustInvalidDataExceptionProvider
     * @param $tags
     * @param $timestamp
     * @expectedException \oat\taoTests\models\runner\time\InvalidDataException
     */
    public function testAdjustInvalidDataException($tags, $timestamp)
    {
        $timer = new QtiTimer();
        $timer->adjust($tags, $timestamp);
    }

    /**
     * @dataProvider adjustInconsistentRangeProvider
     * @param $timer
     * @param $tags
     * @param $duration
     * @expectedException \oat\taoTests\models\runner\time\InconsistentRangeException
     */
    public function testAdjustInconsistentRangeException($timer, $tags, $duration)
    {
        $timer->adjust($tags, $duration);
    }

    /**
     * Test the QtiTimer::compute()
     */
    public function testCompute()
    {
        $timer = new QtiTimer();
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        $timer->start($tags, 1459335000.0000);
        $timer->end($tags, 1459335020.0000);
        $timer->adjust($tags, 10);

        $this->assertEquals(20, $timer->compute([], TimePoint::TARGET_SERVER));
        $this->assertEquals(10, $timer->compute([], TimePoint::TARGET_CLIENT));
    }

    /**
     * @expectedException \oat\taoTests\models\runner\time\InconsistentCriteriaException
     */
    public function testComputeInconsistentCriteriaException()
    {
        $timer = new QtiTimer();
        $timer->compute([], TimePoint::TARGET_ALL);
    }

    /**
     * Test the QtiTimer::timeout()
     */
    public function testTimeout()
    {
        $timer = new QtiTimer();
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        $timer->start($tags, 1459335000.0000);
        $timer->end($tags, 1459335020.0000);
        $timer->adjust($tags, 10);

        $this->assertFalse($timer->timeout(21, [], TimePoint::TARGET_SERVER));
        $this->assertTrue($timer->timeout(19, [], TimePoint::TARGET_SERVER));
        $this->assertTrue($timer->timeout(20, [], TimePoint::TARGET_SERVER));
        $this->assertFalse($timer->timeout(11, [], TimePoint::TARGET_CLIENT));
        $this->assertTrue($timer->timeout(9, [], TimePoint::TARGET_CLIENT));
        $this->assertTrue($timer->timeout(10, [], TimePoint::TARGET_CLIENT));
    }

    /**
     * Test the QtiTimer::setStorage()
     */
    public function testSetStorage()
    {
        $storage = new QtiTimeStorage('fake_session_id');
        $timer = new QtiTimer();
        $this->assertEquals(null, $timer->getStorage());
        $timer->setStorage($storage);
        $this->assertEquals($storage, $timer->getStorage());
    }

    /**
     * Test the QtiTimer::getStorage()
     */
    public function testGetStorage()
    {
        $storage = new QtiTimeStorage('fake_session_id');
        $timer = new QtiTimer();
        $this->assertEquals(null, $timer->getStorage());
        $timer->setStorage($storage);
        $this->assertEquals($storage, $timer->getStorage());
    }

    /**
     * Test the QtiTimer::save()
     */
    public function testSave()
    {
        $timer = new QtiTimer();
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        $timer->start($tags, 1459335000.0000);
        $timer->end($tags, 1459335020.0000);
        $storage = new QtiTimeStorage('fake_session_id');
        $timer->setStorage($storage);
        $result = $timer->save();
        $this->assertEquals($timer, $result);

        $loadedTimer = $timer->load();
        $this->assertEquals($loadedTimer, $result);

        $timeLine = $this->getTimeLine($loadedTimer);
        $timePoints = $timeLine->getPoints();

        $this->assertEquals(2, count($timePoints));
        $this->assertEquals(1459335000.0000, $timePoints[0]->getTimestamp());
        $this->assertEquals(TimePoint::TARGET_SERVER, $timePoints[0]->getTarget());
        $this->assertEquals(TimePoint::TYPE_START, $timePoints[0]->getType());
        $this->assertEquals(1459335020.0000, $timePoints[1]->getTimestamp());
        $this->assertEquals(TimePoint::TARGET_SERVER, $timePoints[1]->getTarget());
        $this->assertEquals(TimePoint::TYPE_END, $timePoints[1]->getType());
    }

    /**
     * @expectedException \oat\taoTests\models\runner\time\InvalidStorageException
     */
    public function testSaveInvalidStorageException()
    {
        $timer = new QtiTimer();
        $timer->save();
    }

    /**
     * Test the QtiTimer::load()
     */
    public function testLoad()
    {
        $timer = new QtiTimer();
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        $timer->start($tags, 1459335000.0000);
        $timer->end($tags, 1459335020.0000);
        $storage = new QtiTimeStorage('fake_session_id');
        $timer->setStorage($storage);
        $timer->save();

        $newTimer = new QtiTimer();
        $newStorage = new QtiTimeStorage('fake_session_id');
        $timeLine = $this->getTimeLine($newTimer);
        $this->assertEquals([], $timeLine->getPoints());

        $newTimer->setStorage($newStorage);
        $newTimer = $newTimer->load();
        $timeLine = $this->getTimeLine($newTimer);

        $timePoints = $timeLine->getPoints();
        $this->assertEquals(2, count($timePoints));
        $this->assertEquals(1459335000.0000, $timePoints[0]->getTimestamp());
        $this->assertEquals(TimePoint::TARGET_SERVER, $timePoints[0]->getTarget());
        $this->assertEquals(TimePoint::TYPE_START, $timePoints[0]->getType());
        $this->assertEquals(1459335020.0000, $timePoints[1]->getTimestamp());
        $this->assertEquals(TimePoint::TARGET_SERVER, $timePoints[1]->getTarget());
        $this->assertEquals(TimePoint::TYPE_END, $timePoints[1]->getType());
    }

    /**
     * @expectedException \oat\taoTests\models\runner\time\InvalidStorageException
     */
    public function testLoadInvalidStorageException()
    {
        $timer = new QtiTimer();
        $timer->load();
    }

    /**
     * @expectedException \oat\taoTests\models\runner\time\InvalidDataException
     */
    public function testLoadInvalidDataException()
    {
        $timer = new QtiTimer();
        $storage = new QtiTimeStorage('fake_session_id');
        $timer->setStorage($storage);
        $this->setTimeLine($timer, new \stdClass());
        $timer->save();
        $timer->load();
    }



    //DATA PROVIDERS
    /**
     * @return array
     */
    public function startInvalidDataExceptionProvider()
    {
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        return [
            [
                $tags,
                'wrong timestamp',
            ],
        ];
    }

    /**
     * @return array
     */
    public function endInvalidDataExceptionProvider()
    {
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        return [
            [
                $tags,
                'wrong timestamp',
            ],
        ];
    }

    /**
     * @return array
     */
    public function adjustDataProvider()
    {
        return [
            [
                1459335000.0000,
                1459335020.0000,
                10,
                10
            ],
            [
                1459335000.0000,
                1459335020.0000,
                null,
                20
            ]
        ];
    }

    /**
     * @return array
     */
    public function adjustInvalidDataExceptionProvider()
    {
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        return [
            [
                $tags,
                'wrong timestamp',
            ],
        ];
    }

    /**
     * @return array
     */
    public function adjustInconsistentRangeProvider()
    {
        $emptyTimer = new QtiTimer();
        $timer = new QtiTimer();
        $tags = [
            'test_fake_id',
            'test_part_fake_id',
            'section_fake_id',
            'item_fake_id',
            'item_fake_id#0',
            'item_fake_id#0-1',
            'item_fake_href',
        ];
        $timer->start($tags, 1459335000.0000);
        $timer->end($tags, 1459335010.0000);

        return [
            [
                $emptyTimer, //timer with empty time line
                $tags,
                1,
            ],
            [
                $timer, //timer with only one time point in time line
                $tags,
                11,
            ],
        ];
    }



    //MOCKS

    /**
     * @param QtiTimer $timer
     * @return QtiTimeLine
     */
    private function getTimeLine(QtiTimer $timer)
    {
        $reflectionClass = new ReflectionClass('oat\taoQtiTest\models\runner\time\QtiTimer');
        $reflectionProperty = $reflectionClass->getProperty('timeLine');
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue($timer);
    }

    /**
     * @param QtiTimer $timer
     * @param mixed $timeLine
     * @return QtiTimeLine
     */
    private function setTimeLine(QtiTimer $timer, $timeLine)
    {
        $reflectionClass = new ReflectionClass('oat\taoQtiTest\models\runner\time\QtiTimer');
        $reflectionProperty = $reflectionClass->getProperty('timeLine');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($timer, $timeLine);
    }

}