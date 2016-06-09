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
 * Interface Timer
 *
 * Describes the API needed yo build and manage a timer.
 * A Timer is represented by a TimeLine on which it will apply actions and constraints.
 * The actions are:
 * - add time ranges by setting start and end TimePoints (SERVER)
 * - adjust time ranges by settings intermediate start and end TimePoint (CLIENT)
 * - compute duration for particular targets (SERVER or CLIENT)
 *
 * The constraints are:
 * - coherence of time ranges
 * - time limits and timeouts
 *
 * A timer is also responsible of its storage.
 *
 * @package oat\taoTests\models\runner\time
 */
interface Timer
{
    /**
     * Adds a "server start" TimePoint at a particular timestamp for the provided tags
     * @param string|array $tags
     * @param float $timestamp
     * @return Timer
     * @throws TimeException
     */
    public function start($tags, $timestamp);

    /**
     * Adds a "server end" TimePoint at a particular timestamp for the provided tags
     * @param string|array $tags
     * @param float $timestamp
     * @return Timer
     * @throws TimeException
     */
    public function end($tags, $timestamp);

    /**
     * Adds "client start" and "client end" TimePoint based on the provided duration for particular tags
     * @param string|array $tags
     * @param float $duration
     * @return Timer
     * @throws TimeException
     */
    public function adjust($tags, $duration);

    /**
     * Computes the total duration represented by the filtered TimePoints
     * @param string|array $tags A tag or a list of tags to filter
     * @param int $target The type of target TimePoint to filter
     * @return float Returns the total computed duration
     * @throws TimeException
     */
    public function compute($tags, $target);

    /**
     * Checks if the duration of a TimeLine subset reached the timeout
     * @param float $timeLimit The time limit against which compare the duration
     * @param string|array $tags A tag or a list of tags to filter
     * @param int $target The type of target TimePoint to filter
     * @return bool Returns true if the timeout is reached
     * @throws TimeException
     */
    public function timeout($timeLimit, $tags, $target);

    /**
     * Sets the storage used to maintain the data
     * @param TimeStorage $storage
     * @return Timer
     */
    public function setStorage(TimeStorage $storage);

    /**
     * Gets the storage used to maintain the data
     * @return TimeStorage
     */
    public function getStorage();

    /**
     * Saves the data to the storage
     * @return Timer
     * @throws \Exception if any error occurs
     */
    public function save();

    /**
     * Loads the data from the storage
     * @return Timer
     * @throws \Exception if any error occurs
     */
    public function load();
}