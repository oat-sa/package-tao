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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

namespace oat\taoTestLinear\controller;

use oat\taoTestLinear\model\TestExecutionState;

/**
 * Test Runner
 *
 * @author Open Assessment Technologies SA
 * @package taoTestLinear
 * @license GPL-2.0
 *
 */
class TestRunner extends \tao_actions_ServiceModule {

    /**
     * initialize the services
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * The test runner
     */
    public function index() {
        
        if (!$this->hasRequestParameter('LinearTestCompilation')) {
            throw new \common_exception_MissingParameter('LinearTestCompilation');
        }
        
        // get current state
        $stateString = $this->getState();
        if (is_null($stateString)) {
            $execution = TestExecutionState::initNew($this->getServiceCallId(), $this->getRequestParameter('LinearTestCompilation'));
            $this->setState($execution->toString());
        } else {
            $execution = TestExecutionState::fromString($stateString);
        }
        
        $this->setData('itemServiceApi', $this->buildItemScript($execution));
        $this->setData('previous', $execution->hasPrevious());

        $this->setData('client_config_url', $this->getClientConfigUrl());
        $this->setData('client_timeout', $this->getClientTimeout());
        
        $this->setView('TestRunner/index.tpl');
        
    }
    
    public function next() {
        
        $stateString = $this->getState();
        if (is_null($stateString)) {
            throw new \common_exception_Error('Called next on a non existing test execution');
        }
        
        $execution = TestExecutionState::fromString($stateString);
        
        if ($execution->hasNext()) {
            $execution->next();
            $this->setState($execution->toString());
            
            $api = $this->buildItemScript($execution);
        } else {
            $api = null;
        }
        
        $this->returnJson(array(
            'api' => $api,
            'next' => $execution->hasNext(),
            'previous' => $execution->hasPrevious()
        ));

    }

    public function previous() {

        $stateString = $this->getState();
        if (is_null($stateString)) {
            throw new \common_exception_Error('Called previous on a non existing test execution');
        }

        $execution = TestExecutionState::fromString($stateString);

        if ($execution->hasPrevious()) {
            $execution->previous();
            $this->setState($execution->toString());

            $api = $this->buildItemScript($execution);
        } else {
            $api = null;
        }
        $this->returnJson(array(
                'api' => $api,
                'next' => $execution->hasNext(),
                'previous' => $execution->hasPrevious()
            ));

    }

    protected function buildItemScript($execution) {
        $serviceCall = $execution->getCurrentServiceCall();
        $itemCallId = $execution->getItemServiceCallId();
        
        return \tao_helpers_ServiceJavascripts::getServiceApi($serviceCall, $itemCallId);
    }   
    
}