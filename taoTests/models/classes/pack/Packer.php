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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoTests\models\pack;

use \core_kernel_classes_Resource;
use \taoTests_models_classes_TestsService;
use \common_exception_NoImplementation;
use \ReflectionClass;
use \ReflectionException;
use \common_Exception;
use \Exception;

/**
 * The Test Packer calls the packable class for the given test
 *
 * @package taoTests
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class Packer
{

    /**
     * The test to pack
     * @var core_kernel_classes_Resource
     */
    private $test;


    /**
     * The test service
     * @var taoTests_models_classes_TestService
     */
    private $testService;

    /**
     * Create a packer for a test
     * @param core_kernel_classes_Resource $test
     */
    public function __construct(core_kernel_classes_Resource $test){
        $this->test = $test;
        $this->testService = taoTests_models_classes_TestsService::singleton();
    }

    /**
     * Get the packer for the test regarding it's implementation.
     *
     * @return Packable the test packer implementation
     * @throws common_exception_NoImplementation
     */
    private function getTestPacker(){

        //look at the item model
        $testModel = $this->testService->getTestModel($this->test);
        if(is_null($testModel)){
            throw new common_exception_NoImplementation('No test model for test '.$this->test->getUri());
        }

        //get the testModel implementation for this model
        $impl = $this->testService->getTestModelImplementation($testModel);
        if(is_null($impl)){
            throw new common_exception_NoImplementation('No implementation for model '.$testModel->getUri());
        }

        //then retrieve the packer class and instantiate it
        $packerClass = new ReflectionClass($impl->getPackerClass());
        if(is_null($packerClass) || !$packerClass->implementsInterface('oat\taoTests\models\pack\Packable')){
            throw new common_exception_NoImplementation('The packer class seems to be not implemented');
        }

        return $packerClass->newInstance();
    }

    /**
     * Pack a test.
     *
     * @return TestPack of the test. It can be serialized directly.
     * @throws common_Exception
     */
    public function pack(){

        try{
            //call the factory to get the itemPacker implementation
            $testPacker = $this->getTestPacker();

            //then create the pack
            $testPack = $testPacker->packTest($this->test);

        } catch(Exception $e){
            throw new common_Exception('The test '. $this->test->getUri() .' cannot be packed : ' . $e->getMessage());
        }

        return $testPack;
    }
}
?>
