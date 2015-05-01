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

namespace oat\taoTestLinear\model;

use taoTests_models_classes_TestCompiler;
use common_report_Report;
use core_kernel_classes_Resource;
use tao_models_classes_service_ServiceCall;

/**
 * Compiles a test and item
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoTestLinear
 */
class TestExecutionState
{
    private $testExecutionId = null;

    private $compilationId = null;

    private $current = null;

    private $itemExecutions;


    static public function fromString($string)
    {
        $json = json_decode($string, true);
        return new self($json['testExecutionId'], $json['compilationId'], $json['itemExecutions'], $json['current']);
    }
    
    static public function initNew($testExecutionId, $compilationId)
    {
        return new self($testExecutionId, $compilationId, array(), 0);
    }
    
    protected function __construct($testExecutionId, $compilationId, $route, $position)
    {
        $this->testExecutionId = $testExecutionId;
        $this->compilationId = $compilationId;
        $this->current = $position;
        if (empty($route)) {
            $itemKeys = array_keys(TestRunnerService::singleton()->getItemData($this->compilationId));
            $route[$position] = array(
                'itemIndex' => reset($itemKeys),
                'callId' => $testExecutionId.'_'.$position,
            );
        }
        $this->itemExecutions = $route;

    }
    
    public function getCurrentServiceCall()
    {
        $itemData = TestRunnerService::singleton()->getItemData($this->compilationId);
        $serviceCall = \tao_models_classes_service_ServiceCall::fromString($itemData[$this->itemExecutions[$this->current]['itemIndex']]);
        return $serviceCall;
    }

    public function getItemServiceCallId()
    {
        return $this->itemExecutions[$this->current]['callId'];
    }


    public function hasNext() {
        $itemKeys = array_keys(TestRunnerService::singleton()->getItemData($this->compilationId));
        return (isset($itemKeys[$this->current + 1]));
    }

    public function next()
    {
        $itemKeys = array_keys(TestRunnerService::singleton()->getItemData($this->compilationId));
        if (isset($itemKeys[$this->current + 1])) {
            $this->current++;
            if (!isset($this->itemExecutions[$this->current])) {
                $this->itemExecutions[$this->current] = array(
                    'itemIndex' => $itemKeys[$this->current],
                    'callId' => $this->testExecutionId.'_'.$this->current
                );
            }
        } else {
            throw new \common_Exception('next called on last Item');
        }
    }

    public function hasPrevious() {
        $itemKeys = array_keys(TestRunnerService::singleton()->getItemData($this->compilationId));
        return (isset($itemKeys[$this->current - 1]) && TestRunnerService::singleton()->getPrevious($this->compilationId));
    }

    public function previous()
    {
        $itemKeys = array_keys(TestRunnerService::singleton()->getItemData($this->compilationId));
        if (isset($itemKeys[$this->current - 1]) && TestRunnerService::singleton()->getPrevious($this->compilationId)) {
            $this->current--;
            if (!isset($this->itemExecutions[$this->current])) {
                $this->itemExecutions[$this->current] = array(
                    'itemIndex' => $itemKeys[$this->current],
                    'callId' => $this->testExecutionId.'_'.$this->current
                );
            }
        } else {
            throw new \common_Exception('previous called on first Item');
        }
    }

    public function toString() {
        return json_encode(array(
            'testExecutionId' => $this->testExecutionId,
            'compilationId' => $this->compilationId,
            'current' => $this->current,
            'itemExecutions' => $this->itemExecutions
        ));
    }
}