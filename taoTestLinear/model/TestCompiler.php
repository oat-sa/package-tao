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
class TestCompiler extends taoTests_models_classes_TestCompiler
{
    const TESTRUNNER_SERVICE = "http://www.tao.lu/Ontologies/TAOTest.rdf#ServiceLinearTestRunner";
    
    const TESTRUNNER_PARAMETER = "http://www.tao.lu/Ontologies/TAOTest.rdf#FormalParamLinearTestRunner";
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_Compiler::compile()
     */
    public function compile() {
        
        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, __('Test Compilation'));
        
        $map = array();
        $model = new TestModel();
        foreach ($model->getItems($this->getResource()) as $item) {
            $subReport = $this->subCompile($item);
            $report->add($subReport);
            if ($subReport->getType() == common_report_Report::TYPE_SUCCESS) {
                $serviceCall = $subReport->getdata();
                $map[$item->getUri()] = $serviceCall->serializeToString();
            } else {
                $report->setType(common_report_Report::TYPE_ERROR);
            }
        }

        if (count($map) === 0) {
            $report->setType(common_report_Report::TYPE_ERROR);
            $report->setMessage(__("A Test must contain at least one item to be compiled."));
        }

        if ($report->getType() === common_report_Report::TYPE_SUCCESS) {
            $config = array();
            $private = $this->spawnPrivateDirectory();
            $file = $private->getPath().'data.json';
            $config['items'] = $map;
            $config = array_merge($config, $model->getConfig($this->getResource()));

            file_put_contents($file, json_encode($config));
            $report->setData($this->buildServiceCall($private));
        }
        
        return $report;
    }
    
    /**
     * 
     * @param tao_models_classes_service_StorageDirectory $private
     * @return tao_models_classes_service_ServiceCall
     */
    protected function buildServiceCall(\tao_models_classes_service_StorageDirectory $private) {
        $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(self::TESTRUNNER_SERVICE));
    
        $param = new \tao_models_classes_service_ConstantParameter(
            // storageDirectory id passed to testrunner 
            new core_kernel_classes_Resource(self::TESTRUNNER_PARAMETER),
            $private->getId()
        );
        $service->addInParameter($param);
    
        return $service;
    }
}