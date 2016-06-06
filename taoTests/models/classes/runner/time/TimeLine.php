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

namespace oat\taoTests\models\runner\time;

/**
 * Interface TimeLine
 * 
 * Describes the API needed to build and manage a time line.
 * A TimeLine is represented by a list of TimePoint.
 * These TimePoint represents the bounds of time ranges.
 * Each time range must be represented by two TimePoint: START and END.
 * 
 * @package oat\taoTests\models\runner\time
 */
interface TimeLine extends \Serializable
{
    /**
     * Gets the list of TimePoint present in the TimeLine
     * @return array
     */
    public function getPoints();
    
    /**
     * Adds another TimePoint inside the TimeLine
     * @param TimePoint $point
     * @return TimeLine
     */
    public function add(TimePoint $point);

    /**
     * Removes all TimePoint corresponding to the provided criteria
     * @param string|array $tag A tag or a list of tags to filter
     * @param int $target The type of target TimePoint to filter
     * @param int $type The type of TimePoint to filter
     * @return int Returns the number of removed TimePoints
     */
    public function remove($tag, $target = TimePoint::TARGET_ALL, $type = TimePoint::TYPE_ALL);

    /**
     * Clears the TimeLine from all its TimePoint
     * @return TimeLine
     */
    public function clear();

    /**
     * Gets a filtered TimeLine, containing the TimePoint corresponding to the provided criteria
     * @param string|array $tag A tag or a list of tags to filter
     * @param int $target The type of target TimePoint to filter
     * @param int $type The type of TimePoint to filter
     * @return TimeLine Returns a subset corresponding to the found TimePoints
     */
    public function filter($tag = null, $target = TimePoint::TARGET_ALL, $type = TimePoint::TYPE_ALL);

    /**
     * Finds all TimePoint corresponding to the provided criteria
     * @param string|array $tag A tag or a list of tags to filter
     * @param int $target The type of target TimePoint to filter
     * @param int $type The type of TimePoint to filter
     * @return array Returns a list of the found TimePoints
     */
    public function find($tag = null, $target = TimePoint::TARGET_ALL, $type = TimePoint::TYPE_ALL);

    /**
     * Computes the total duration represented by the filtered TimePoints
     * @param string|array $tag A tag or a list of tags to filter
     * @param int $target The type of target TimePoint to filter
     * @return float Returns the total computed duration
     * @throws TimeException
     */
    public function compute($tag = null, $target = TimePoint::TARGET_ALL);
}
