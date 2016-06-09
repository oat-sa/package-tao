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

use oat\taoQtiTest\models\runner\time\QtiTimeLine;
use oat\taoTests\models\runner\time\TimePoint;
use oat\tao\test\TaoPhpUnitTestRunner;

/**
 * Test the {@link QtiTimeLine}
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 */
class QtiTimeLineTest extends TaoPhpUnitTestRunner
{

    public function setUp()
    {
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
    }

    /**
     * Test the TimeLine instantiation
     */
    public function testConstructor()
    {
        $timeLine = new QtiTimeLine([
            new TimePoint(['test-a', 'item-a'], 1459519500.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
            new TimePoint(['test-a', 'item-a'], 1459519502.2422, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
        ]);
        $this->assertInstanceOf('oat\taoTests\models\runner\time\TimeLine', $timeLine);
    }

    /**
     * @dataProvider linearTestPointsProvider
     * @param TimePoint[] $points
     */
    public function testSerialize($points)
    {
        $timeLine = new QtiTimeLine($points);
        $timeLineUnserialized = new QtiTimeLine();
        $data = $timeLine->serialize();
        $timeLineUnserialized->unserialize($data);
        $this->assertEquals($timeLine->getPoints(), $timeLineUnserialized->getPoints());
    }

    /**
     * @dataProvider linearTestPointsProvider
     * @param TimePoint[] $points
     */
    public function testUnserialize($points)
    {
        $timeLine = new QtiTimeLine($points);
        $timeLineUnserialized = new QtiTimeLine();
        $data = $timeLine->serialize();
        $timeLineUnserialized->unserialize($data);
        $this->assertEquals($timeLineUnserialized->getPoints(), $timeLine->getPoints());
    }

    /**
     * @expectedException \oat\taoTests\models\runner\time\InvalidDataException
     */
    public function testUnserializeInvalidDataException()
    {
        $timeLine = new QtiTimeLine();
        $data = serialize('string');
        $timeLine->unserialize($data);
    }

    /**
     * Test the QtiTimeLine::getPoints()
     * @dataProvider linearTestPointsProvider
     * @param TimePoint[] $points
     */
    public function testGetPoints($points)
    {
        $timeLine = new QtiTimeLine();
        foreach ($points as $point) {
            $timeLine->add($point);
        }
        $this->assertEquals($points, $timeLine->getPoints());
    }

    /**
     * Test the QtiTimeLine::getPoints()
     */
    public function testAdd()
    {
        $timeLine = new QtiTimeLine();
        $this->assertEquals($timeLine->getPoints(), [], 'The timeline is empty added');

        $point = new TimePoint(['test-A', 'item-A'], 1459519570.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER);
        $result = $timeLine->add($point);
        $this->assertEquals($timeLine, $result);
        $this->assertEquals($timeLine->getPoints(), [$point]);

        $point2 = new TimePoint(['test-A', 'item-B'], 1459519770.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER);
        $timeLine->add($point2);
        $this->assertEquals($timeLine->getPoints(), [$point, $point2]);
    }

    /**
     * Test the QtiTimeLine::remove()
     * @dataProvider linearTestPointsProvider
     * @param TimePoint[] $points
     */
    public function testRemove($points)
    {
        //remove all points
        $timeLine = new QtiTimeLine($points);
        $this->assertEquals($points, $timeLine->getPoints());
        $removed = $timeLine->remove([], TimePoint::TARGET_ALL);
        $this->assertEquals([], $timeLine->getPoints());
        $this->assertEquals(count($points), $removed);

        //remove all points
        $timeLine = new QtiTimeLine($points);
        $removed = $timeLine->remove([], TimePoint::TARGET_ALL, TimePoint::TYPE_ALL);
        $this->assertEquals([], $timeLine->getPoints());
        $this->assertEquals(count($points), $removed);

        //remove all points
        $timeLine = new QtiTimeLine($points);
        $removed = $timeLine->remove([]);
        $this->assertEquals([], $timeLine->getPoints());
        $this->assertEquals(count($points), $removed);

        //remove second points
        $pointToRemove = $points[1];
        $timeLine = new QtiTimeLine($points);
        $removed = $timeLine->remove($pointToRemove->getTags(), $pointToRemove->getTarget(), $pointToRemove->getType());
        $expected = $points;
        unset($expected[1]);
        $this->assertEquals(1, $removed);
        $this->assertEquals($expected, $timeLine->getPoints());

        //remove first four points
        $timeLine = new QtiTimeLine($points);
        $removed = $timeLine->remove($points[0]->getTags(), TimePoint::TARGET_ALL, TimePoint::TYPE_ALL);
        $expected = $points;
        unset($expected[0], $expected[1], $expected[2], $expected[3]);
        $this->assertEquals(4, $removed);
        $this->assertEquals($expected, $timeLine->getPoints());
    }

    /**
     * Test the QtiTimeLine::clear()
     * @dataProvider linearTestPointsProvider
     * @param TimePoint[] $points
     */
    public function testClear($points)
    {
        $timeLine = new QtiTimeLine($points);
        $this->assertEquals($points, $timeLine->getPoints());
        $result = $timeLine->clear();
        $this->assertEquals($timeLine, $result);
        $this->assertEquals([], $timeLine->getPoints());
    }

    /**
     * Test the QtiTimeLine::filter()
     * @dataProvider linearTestPointsProvider
     * @param TimePoint[] $points
     */
    public function testFilter($points)
    {
        $timeLine = new QtiTimeLine($points);
        $filteredTimeLine = $timeLine->filter(null, TimePoint::TARGET_CLIENT);
        foreach ($filteredTimeLine->getPoints() as $point) {
            $this->assertEquals(TimePoint::TARGET_CLIENT, $point->getTarget());
        }

        $timeLine = new QtiTimeLine($points);
        $filteredTimeLine = $timeLine->filter(null, TimePoint::TYPE_ALL, TimePoint::TYPE_START);
        foreach ($filteredTimeLine->getPoints() as $point) {
            $this->assertEquals(TimePoint::TYPE_START, $point->getType());
        }

        $timeLine = new QtiTimeLine($points);
        $filteredTimeLine = $timeLine->filter(null, TimePoint::TARGET_CLIENT, TimePoint::TYPE_START);
        foreach ($filteredTimeLine->getPoints() as $point) {
            $this->assertEquals(TimePoint::TARGET_CLIENT, $point->getTarget());
            $this->assertEquals(TimePoint::TYPE_START, $point->getType());
        }

        $timeLine = new QtiTimeLine($points);
        $filteredTimeLine = $timeLine->filter(['test-a', 'item-a']);
        foreach ($filteredTimeLine->getPoints() as $point) {
            $this->assertEquals(['test-a', 'item-a'], $point->getTags());
        }
    }

    /**
     * Test the QtiTimeLine::find()
     * @dataProvider linearTestPointsProvider
     * @param TimePoint[] $points
     */
    public function testFind($points)
    {
        $timeLine = new QtiTimeLine($points);

        $foundPoints = $timeLine->find(null, TimePoint::TARGET_CLIENT);
        foreach ($foundPoints as $point) {
            $this->assertEquals(TimePoint::TARGET_CLIENT, $point->getTarget());
        }

        $foundPoints = $timeLine->find(null, TimePoint::TARGET_ALL, TimePoint::TYPE_START);
        foreach ($foundPoints as $point) {
            $this->assertEquals(TimePoint::TYPE_START, $point->getType());
        }

        $foundPoints = $timeLine->find(null, TimePoint::TARGET_SERVER, TimePoint::TYPE_START);
        foreach ($foundPoints as $point) {
            $this->assertEquals(TimePoint::TARGET_SERVER, $point->getTarget());
            $this->assertEquals(TimePoint::TYPE_START, $point->getType());
        }

        $foundPoints = $timeLine->find(['test-a', 'item-a']);
        foreach ($foundPoints as $point) {
            $this->assertEquals(['test-a', 'item-a'], $point->getTags());
        }
    }

    /**
     * Test the QtiTimeLine::compute()
     */
    public function testCompute()
    {
        $points = [
            new TimePoint(['test-a', 'item-a'], 1459519500.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
            new TimePoint(['test-a', 'item-a'], 1459519502.2422, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
            new TimePoint(['test-a', 'item-a'], 1459519510.2422, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
            new TimePoint(['test-a', 'item-a'], 1459519512.2422, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
            new TimePoint(['test-a', 'item-b'], 1459519520.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
            new TimePoint(['test-a', 'item-b'], 1459519522.2422, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
            new TimePoint(['test-a', 'item-b'], 1459519530.2422, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
            new TimePoint(['test-a', 'item-b'], 1459519532.2422, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
        ];
        $timeLine = new QtiTimeLine($points);

        $duration = $timeLine->compute(['test-a', 'item-a'], TimePoint::TARGET_SERVER);
        $expectedDuration = $points[3]->getTimestamp() - $points[0]->getTimestamp();
        $this->assertEquals($duration, $expectedDuration);

        $duration = $timeLine->compute(['test-a', 'item-a'], TimePoint::TARGET_CLIENT);
        $expectedDuration = $points[2]->getTimestamp() - $points[1]->getTimestamp();
        $this->assertEquals($duration, $expectedDuration);

        $timeLine = new QtiTimeLine([$points[1], $points[2]]);
        $duration = $timeLine->compute(null, TimePoint::TARGET_ALL);
        $expectedDuration = $points[2]->getTimestamp() - $points[1]->getTimestamp();
        $this->assertEquals($duration, $expectedDuration);
    }

    /**
     * Test the QtiTimeLine::compute()
     * @dataProvider malformedRangeExceptionProvider
     * @expectedException \oat\taoTests\models\runner\time\MalformedRangeException
     * @param TimePoint[] $points
     */
    public function testComputeMalformedRangeException($points)
    {
        $timeLine = new QtiTimeLine($points);
        $timeLine->compute(['test-a', 'item-a']);
    }

    /**
     * Test the QtiTimeLine::compute()
     * @expectedException \oat\taoTests\models\runner\time\IncompleteRangeException
     */
    public function testComputeIncompleteRangeException()
    {
        $timeLine = new QtiTimeLine([
            new TimePoint(['test-a', 'item-a'], 1459519500.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
            new TimePoint(['test-a', 'item-a'], 1459519502.2422, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
            new TimePoint(['test-a', 'item-a'], 1459519510.2422, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
        ]);
        $timeLine->compute(['test-a', 'item-a']);
    }

    /**
     * Test the QtiTimeLine::compute()
     * @expectedException \oat\taoTests\models\runner\time\InconsistentRangeException
     * @dataProvider inconsistentRangeExceptionProvider
     * @param TimePoint[] $points
     */
    public function testInconsistentRangeException($points)
    {
        $timeLine = new QtiTimeLine($points);
        $timeLine->compute(['test-a', 'item-a']);
    }

    /**
     * @return array
     */
    public function linearTestPointsProvider()
    {
        return [
            [
                'linearTest' => [
                    //item-a
                    new TimePoint(['test-a', 'item-a'], 1459519500.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
                    new TimePoint(['test-a', 'item-a'], 1459519502.2422, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                    new TimePoint(['test-a', 'item-a'], 1459519510.2422, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
                    new TimePoint(['test-a', 'item-a'], 1459519512.2422, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
                    //item-b
                    new TimePoint(['test-a', 'item-b'], 1459519600.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
                    new TimePoint(['test-a', 'item-b'], 1459519602.2422, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                    new TimePoint(['test-a', 'item-b'], 1459519610.2422, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
                    new TimePoint(['test-a', 'item-b'], 1459519612.2422, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
                    //item-b
                    new TimePoint(['test-a', 'item-c'], 1459519700.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
                    new TimePoint(['test-a', 'item-c'], 1459519702.2422, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                    new TimePoint(['test-a', 'item-c'], 1459519710.2422, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
                    new TimePoint(['test-a', 'item-c'], 1459519712.2422, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function malformedRangeExceptionProvider()
    {
        return [
            [
                [
                    new TimePoint(['test-a', 'item-a'], 1459519500.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
                    new TimePoint(['test-a', 'item-a'], 1459519502.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
                ],
            ],
            [
                [
                    new TimePoint(['test-a', 'item-a'], 1459519500.2422, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
                    new TimePoint(['test-a', 'item-a'], 1459519502.2422, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function inconsistentRangeExceptionProvider()
    {
        return [
            [
                [
                    new TimePoint(['test-a', 'item-a'], 1459519502.2422, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                    new TimePoint(['test-a', 'item-a'], 1459519510.2422, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
                ]
            ],
            [
                [
                    new TimePoint(['test-a', 'item-a'], 1459519522.2422, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
                    new TimePoint(['test-a', 'item-a'], 1459519510.2422, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
                ]
            ],
        ];
    }
}
