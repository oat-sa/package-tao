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
namespace oat\taoQtiTest\models\runner;

use oat\taoQtiTest\models\runner\config\RunnerConfig;

/**
 * Interface RunnerService
 * 
 * Describes a test runner dedicated service
 * 
 * @package oat\taoQtiTest\models
 */
interface RunnerService
{
    /**
     * Initializes the delivery execution session
     * @param RunnerServiceContext $context
     * @return boolean
     * @throws \common_Exception
     */
    public function init(RunnerServiceContext $context);

    /**
     * Gets the test runner config
     * @return RunnerConfig
     * @throws \common_ext_ExtensionException
     */
    public function getTestConfig();

    /**
     * Gets the test definition data
     * @param RunnerServiceContext $context
     * @return array
     * @throws \common_Exception
     */
    public function getTestData(RunnerServiceContext $context);

    /**
     * Gets the test context object
     * @param RunnerServiceContext $context
     * @return array
     * @throws \common_Exception
     */
    public function getTestContext(RunnerServiceContext $context);
    
    /**
     * Gets the map of the test items
     * @param RunnerServiceContext $context
     * @return array
     * @throws \common_Exception
     */
    public function getTestMap(RunnerServiceContext $context);

    /**
     * Gets the rubrics related to the current session state
     * @param RunnerServiceContext $context
     * @return mixed
     * @throws \common_Exception
     */
    public function getRubrics(RunnerServiceContext $context);

    /**
     * Gets definition data of a particular item
     * @param RunnerServiceContext $context
     * @param $itemRef
     * @return mixed
     * @throws \common_Exception
     */
    public function getItemData(RunnerServiceContext $context, $itemRef);

    /**
     * Gets the state of a particular item
     * @param RunnerServiceContext $context
     * @param $itemRef
     * @return array
     * @throws \common_Exception
     */
    public function getItemState(RunnerServiceContext $context, $itemRef);

    /**
     * Sets the state of a particular item
     * @param RunnerServiceContext $context
     * @param $itemRef
     * @param $state
     * @return boolean
     * @throws \common_Exception
     */
    public function setItemState(RunnerServiceContext $context, $itemRef, $state);

    /**
     * Stores the response of a particular item
     * @param RunnerServiceContext $context
     * @param $itemRef
     * @param $response
     * @return boolean
     * @throws \common_Exception
     */
    public function storeItemResponse(RunnerServiceContext $context, $itemRef, $response);

    /**
     * Should we display feedbacks
     * @param RunnerServiceContext $context
     * @return boolean
     * @throws \common_Exception
     */
    public function displayFeedbacks(RunnerServiceContext $context);

    /**
     * Get feedback definition
     * @param RunnerServiceContext $context
     * @param string $itemRef  the item reference
     * @return array the feedbacks data
     * @throws \common_exception_InvalidArgumentType
     */
    public function getFeedbacks(RunnerServiceContext $context, $itemRef);

    /**
     * Get the current item session
     * @param RunnerServiceContext $context
     * @throws \common_Exception
     */
    public function getItemSession(RunnerServiceContext $context);

    /**
     * Moves the current position to the provided scoped reference.
     * @param RunnerServiceContext $context
     * @param $direction
     * @param $scope
     * @param $ref
     * @return boolean
     * @throws \common_Exception
     */
    public function move(RunnerServiceContext $context, $direction, $scope, $ref);

    /**
     * Skips the current position to the provided scoped reference
     * @param RunnerServiceContext $context
     * @param $scope
     * @param $ref
     * @return boolean
     * @throws \common_Exception
     */
    public function skip(RunnerServiceContext $context, $scope, $ref);

    /**
     * Handles a test timeout
     * @param RunnerServiceContext $context
     * @param $scope
     * @param $ref
     * @return boolean
     * @throws \common_Exception
     */
    public function timeout(RunnerServiceContext $context, $scope, $ref);
    
    /**
     * Exits the test before its end
     * @param RunnerServiceContext $context
     * @return boolean
     * @throws \common_Exception
     */
    public function exitTest(RunnerServiceContext $context);
    
    /**
     * Finishes the test
     * @param RunnerServiceContext $context
     * @return boolean
     * @throws \common_Exception
     */
    public function finish(RunnerServiceContext $context);

    /**
     * Sets the test to paused state
     * @param RunnerServiceContext $context
     * @return boolean
     * @throws \common_Exception
     */
    public function pause(RunnerServiceContext $context);

    /**
     * Resumes the test from paused state
     * @param RunnerServiceContext $context
     * @return boolean
     * @throws \common_Exception
     */
    public function resume(RunnerServiceContext $context);

    /**
     * Checks if the test is still valid
     * @param RunnerServiceContext $context
     * @return boolean
     * @throws \common_Exception
     */
    public function check(RunnerServiceContext $context);


    /**
     * Update the test timers duration
     * @param RunnerServiceContext $context
     * @param int|float $duration
     * @return boolean
     * @throws \common_Exception
     */
    public function updateTimers(RunnerServiceContext $context, $duration);

}
