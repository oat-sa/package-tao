<?php
/**
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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoTests\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTests\models\runner\time\TimePoint;

class TimerPointTest extends TaoPhpUnitTestRunner
{
    /**
     * tests initialization
     */
    public function setUp()
    {
        parent::setUp();
        TaoPhpUnitTestRunner::initTest();
    }

    /**
     * @dataProvider testSerializeProvider
     * @param $tags
     * @param $timestamp
     * @param $type
     * @param $target
     */
    public function testSerialize($tags, $timestamp, $type, $target)
    {
        $timePoint = new TimePoint($tags, $timestamp, $type, $target);
        $timePointUnserialized = new TimePoint();
        $data = $timePoint->serialize();
        $timePointUnserialized->unserialize($data);
        $this->assertEquals($timePoint->getTimestamp(), $timePointUnserialized->getTimestamp());
        $this->assertEquals($timePoint->getTarget(), $timePointUnserialized->getTarget());
        $this->assertEquals($timePoint->getTags(), $timePointUnserialized->getTags());
        $this->assertEquals($timePoint->getType(), $timePointUnserialized->getType());
    }

    /**
     * @dataProvider testSerializeProvider
     * @param $tags
     * @param $timestamp
     * @param $type
     * @param $target
     */
    public function testUnserialize($tags, $timestamp, $type, $target)
    {
        $timePoint = new TimePoint($tags, $timestamp, $type, $target);
        $timePointUnserialized = new TimePoint();
        $data = $timePoint->serialize();
        $timePointUnserialized->unserialize($data);
        $this->assertEquals($timePointUnserialized->getTimestamp(), $timePoint->getTimestamp());
        $this->assertEquals($timePointUnserialized->getTarget(), $timePoint->getTarget());
        $this->assertEquals($timePointUnserialized->getTags(), $timePoint->getTags());
        $this->assertEquals($timePointUnserialized->getType(), $timePoint->getType());
    }

    /**
     * Test TimePoint::addTag() method
     */
    public function testAddTag()
    {
        $timePoint = new TimePoint();
        $timePoint->addTag('tag1');
        $this->assertEquals($timePoint->getTags(), ['tag1']);

        $timePoint->addTag('tag2');
        $this->assertEquals($timePoint->getTags(), ['tag1', 'tag2']);
    }

    /**
     * Test TimePoint::removeTag() method
     */
    public function testRemoveTag()
    {
        $timePoint = new TimePoint();
        $timePoint->setTags(['tag1', 'tag2', 'tag3']);
        $this->assertEquals($timePoint->getTags(), ['tag1', 'tag2', 'tag3']);

        $timePoint->removeTag('tag2');
        $this->assertEquals($timePoint->getTags(), ['tag1', 'tag3']);

        $timePoint->removeTag('tag1');
        $this->assertEquals($timePoint->getTags(), ['tag3']);

        $timePoint->removeTag('tag3');
        $this->assertEquals($timePoint->getTags(), []);
    }

    /**
     * Test TimePoint::setTags() method
     */
    public function testSetTags()
    {
        $timePoint = new TimePoint();
        $this->assertEquals($timePoint->getTags(), []);

        $timePoint->setTags(['tag1', 'tag2', 'tag3']);
        $this->assertEquals($timePoint->getTags(), ['tag1', 'tag2', 'tag3']);

        $timePoint->setTags(['tag4', 'tag5', 'tag6']);
        $this->assertEquals($timePoint->getTags(), ['tag4', 'tag5', 'tag6']);

        $timePoint->setTags('stringTag');
        $this->assertEquals($timePoint->getTags(), ['stringTag']);
    }

    /**
     * Test TimePoint::getRef() method
     */
    public function testGetRef()
    {
        $timePoint = new TimePoint(['a', 'b', 'c']);
        $timePoint2 = new TimePoint(['c', 'b', 'a']);
        $timePoint3 = new TimePoint(['a', 'b', 'x']);
        $this->assertEquals($timePoint->getRef(), $timePoint2->getRef());
        $this->assertNotEquals($timePoint->getRef(), $timePoint3->getRef());
    }

    /**
     * Test TimePoint::testGetTag() method
     */
    public function testGetTag()
    {
        $timePoint = new TimePoint(['a', 'b', 'c']);
        $this->assertEquals('a', $timePoint->getTag(0));
        $this->assertEquals('b', $timePoint->getTag(1));
        $this->assertEquals('c', $timePoint->getTag(2));
    }

    /**
     * Test TimePoint::match() method
     */
    public function testMatch()
    {
        $timePoint = new TimePoint(['a', 'b', 'c'], null, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT);

        $this->assertTrue($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_ALL, TimePoint::TARGET_ALL));
        $this->assertTrue($timePoint->match(['b', 'c'], TimePoint::TYPE_ALL, TimePoint::TARGET_ALL));
        $this->assertTrue($timePoint->match(null, TimePoint::TYPE_ALL, TimePoint::TARGET_ALL));
        $this->assertTrue($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_START, TimePoint::TARGET_CLIENT));
        $this->assertTrue($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_START));
        $this->assertTrue($timePoint->match(['a', 'b', 'c']));

        $this->assertFalse($timePoint->match(['b', 'c', 'd'], TimePoint::TYPE_ALL, TimePoint::TARGET_ALL));
        $this->assertFalse($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_END, TimePoint::TARGET_ALL));
        $this->assertFalse($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_START, TimePoint::TARGET_SERVER));
        $this->assertFalse($timePoint->match(['a', 'b', 'c'], TimePoint::TYPE_END, TimePoint::TARGET_SERVER));
    }

    /**
     * Test TimePoint::compare() method
     * @dataProvider testCompareProvider
     */
    public function testCompare(TimePoint $firstPoint, TimePoint $secondPoint, $expectedResult)
    {
        $this->assertEquals($expectedResult, $firstPoint->compare($secondPoint));
    }

    /**
     * Test TimePoint::sort() method
     * @dataProvider testSortProvider
     */
    public function testSort(array $range, array $expectedResult)
    {
        $this->assertEquals($expectedResult, TimePoint::sort($range));
    }

    /**
     * @return array
     */
    public function testSerializeProvider()
    {
        return [
            [
                'abc',
                1459335349,
                TimePoint::TYPE_START,
                TimePoint::TARGET_SERVER,
            ],
            [
                ['a', 'b', 'c'],
                1459335572,
                TimePoint::TYPE_START,
                TimePoint::TARGET_SERVER,
            ],
            [
                null,
                null,
                null,
                null,
            ],
        ];
    }

    /**
     * @return array
     */
    public function testCompareProvider()
    {
        return [
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                new TimePoint(null, 1459335311, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                -11 * TimePoint::PRECISION,
            ],
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                new TimePoint(null, 1459335289, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                11 * TimePoint::PRECISION,
            ],
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                new TimePoint(null, 1459335300, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
                -1,
            ],
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                1,
            ],
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                1,
            ],
            [
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
                new TimePoint(null, 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
                -1,
            ],
        ];
    }

    /**
     * @return array
     */
    public function testSortProvider()
    {
        $points1 = [
            0 => new TimePoint(['item-1', 'item-1#0'], 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
            1 => new TimePoint(['item-2', 'item-2#0'], 1459335320, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
            2 => new TimePoint(['item-1', 'item-1#1'], 1459335340, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),

            3 => new TimePoint(['item-1', 'item-1#0'], 1459335310, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
            4 => new TimePoint(['item-2', 'item-2#0'], 1459335330, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
            5 => new TimePoint(['item-1', 'item-1#1'], 1459335350, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
        ];

        $points2 = [
            0 => new TimePoint(['item-1', 'item-1#0'], 1459335300, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
            1 => new TimePoint(['item-2', 'item-2#0'], 1459335320, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
            2 => new TimePoint(['item-1', 'item-1#1'], 1459335340, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),

            3 => new TimePoint(['item-1', 'item-1#0'], 1459335305, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
            4 => new TimePoint(['item-2', 'item-2#0'], 1459335325, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),
            5 => new TimePoint(['item-1', 'item-1#1'], 1459335345, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT),

            6 => new TimePoint(['item-1', 'item-1#0'], 1459335315, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
            7 => new TimePoint(['item-2', 'item-2#0'], 1459335335, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
            8 => new TimePoint(['item-1', 'item-1#1'], 1459335355, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),

            9 => new TimePoint(['item-1', 'item-1#0'], 1459335310, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
            10 => new TimePoint(['item-2', 'item-2#0'], 1459335330, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
            11 => new TimePoint(['item-1', 'item-1#1'], 1459335350, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT),
        ];
        
        $points3 = [
            0 => new TimePoint(['item-1', 'item-1#0'], 1460116638.5226, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
            1 => new TimePoint(['item-1', 'item-1#0'], 1460116645.4868, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),

            2 => new TimePoint(['item-1', 'item-1#0'], 1460116646.2684, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
            3 => new TimePoint(['item-1', 'item-1#0'], 1460116653.7042, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),

            4 => new TimePoint(['item-1', 'item-1#0'], 1460116733.6273, TimePoint::TYPE_START, TimePoint::TARGET_SERVER),
            5 => new TimePoint(['item-1', 'item-1#0'], 1460116784.4006, TimePoint::TYPE_END, TimePoint::TARGET_SERVER),
        ];


        return [
            [
                $points1,
                [
                    $points1[1], $points1[4], // item-2#0
                    $points1[2], $points1[5], // item-1#1
                    $points1[0], $points1[3], // item-1#0
                ],
            ],
            [
                $points2,
                [
                    $points2[4], $points2[10],  // item-2#0 client
                    $points2[1], $points2[7],   // item-2#0 server
                    $points2[5], $points2[11],  // item-1#1 client
                    $points2[2], $points2[8],   // item-1#1 server
                    $points2[3], $points2[9],   // item-1#0 client
                    $points2[0], $points2[6],   // item-1#0 server
                ],
            ],
            [
                $points3,
                $points3,
            ]
        ];
    }
}