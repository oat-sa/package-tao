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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoTestLinear\test\model;



use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTestLinear\model\TestExecutionState;


/**
 * Test Execution test
 *
 * @access public
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package taoTestLinear
 */
class TestExecutionStateTest extends TaoPhpUnitTestRunner {

    private $testRunnerService = null;

    public function setUp(){
        TaoPhpUnitTestRunner::initTest();

        $this->testRunnerService = $this->getMockBuilder('oat\taoTestLinear\model\TestRunnerService')
            ->disableOriginalConstructor()
            ->setMethods(array('getItemData', 'getPrevious'))
            ->getMock();

        $ref = new \ReflectionProperty('tao_models_classes_Service', 'instances');
        $ref->setAccessible(true);
        $ref->setValue(null, array('oat\taoTestLinear\model\TestRunnerService' => $this->testRunnerService));
    }

    public function tearDown(){
        $ref = new \ReflectionProperty('tao_models_classes_Service', 'instances');
        $ref->setAccessible(true);
        $ref->setValue(null, array('oat\taoTestLinear\model\TestRunnerService' => null));
    }


    public function testInitNew(){
        $testExecutionId = 'myTestId';
        $compilationId = 'myCompilationId';

        $this->testRunnerService->expects($this->once())
                ->method('getItemData')
                ->with($compilationId)
                ->willReturn(array());

        $execution = TestExecutionState::initNew($testExecutionId, $compilationId);
        $this->assertInstanceOf('oat\taoTestLinear\model\TestExecutionState', $execution, __('Init New doesn\'t construct Test execution object'));

        $this->assertEquals($testExecutionId.'_0', $execution->getItemServiceCallId());

    }

    public function testHasNext() {
        $testExecutionId = 'myTestId';
        $compilationId = 'myCompilationId';

        $this->testRunnerService->expects($this->exactly(4))
            ->method('getItemData')
            ->with($compilationId)
            ->willReturn(array('myFirstKey', 'mySecondKey'));

        $execution = TestExecutionState::initNew($testExecutionId, $compilationId);

        $hasNext = $execution->hasNext();

        $this->assertTrue($hasNext, __('Should have next'));
        $execution->next();
        $hasNext = $execution->hasNext();
        $this->assertFalse($hasNext, __('Should be at the end'));

    }

    public function testNext(){
        $testExecutionId = 'myTestId';
        $compilationId = 'myCompilationId';

        $this->testRunnerService->expects($this->exactly(2))
            ->method('getItemData')
            ->with($compilationId)
            ->willReturn(array('myFirstKey', 'mySecondKey'));

        $execution = TestExecutionState::initNew($testExecutionId, $compilationId);


        $execution->next();
        $this->assertEquals($testExecutionId.'_1', $execution->getItemServiceCallId());
    }

    /**
     * @expectedException \common_Exception
     */
    public function testNextException(){
        $testExecutionId = 'myTestId';
        $compilationId = 'myCompilationId';

        $this->testRunnerService->expects($this->exactly(2))
            ->method('getItemData')
            ->with($compilationId)
            ->willReturn(array('myFirstKey'));

        $execution = TestExecutionState::initNew($testExecutionId, $compilationId);


        $execution->next();
    }


    public function testHasPrevious(){
        $testExecutionId = 'myTestId';
        $compilationId = 'myCompilationId';

        $this->testRunnerService->expects($this->exactly(4))
            ->method('getItemData')
            ->with($compilationId)
            ->willReturn(array('myFirstKey', 'mySecondKey'));

        $this->testRunnerService->expects($this->exactly(1))
            ->method('getPrevious')
            ->with($compilationId)
            ->willReturn(true);


        $execution = TestExecutionState::initNew($testExecutionId, $compilationId);

        $hasPrevious = $execution->hasPrevious();
        $this->assertFalse($hasPrevious, __('Should be at the beginning'));
        $execution->next();

        $hasPrevious = $execution->hasPrevious();
        $this->assertTrue($hasPrevious, __('Should have previous'));
    }

    public function testHasNotPrevious(){
        $testExecutionId = 'myTestId';
        $compilationId = 'myCompilationId';

        $this->testRunnerService->expects($this->exactly(3))
            ->method('getItemData')
            ->with($compilationId)
            ->willReturn(array('myFirstKey', 'mySecondKey'));

        $this->testRunnerService->expects($this->exactly(1))
            ->method('getPrevious')
            ->with($compilationId)
            ->willReturn(false);


        $execution = TestExecutionState::initNew($testExecutionId, $compilationId);

        $execution->next();

        $hasPrevious = $execution->hasPrevious();
        $this->assertFalse($hasPrevious, __('Should not have previous'));
    }

    public function testPrevious(){
        $testExecutionId = 'myTestId';
        $compilationId = 'myCompilationId';

        $this->testRunnerService->expects($this->exactly(5))
            ->method('getItemData')
            ->with($compilationId)
            ->willReturn(array('myFirstKey', 'mySecondKey'));

        $this->testRunnerService->expects($this->exactly(2))
            ->method('getPrevious')
            ->with($compilationId)
            ->willReturn(true);


        $execution = TestExecutionState::initNew($testExecutionId, $compilationId);

        $execution->next();
        $hasPrevious = $execution->hasPrevious();
        $this->assertTrue($hasPrevious, __('Should have previous'));

        $execution->previous();
        $hasPrevious = $execution->hasPrevious();
        $this->assertFalse($hasPrevious, __('Should not have previous'));

        $this->assertEquals($testExecutionId.'_0', $execution->getItemServiceCallId());
    }

    /**
     * @expectedException \common_Exception
     */
    public function testPreviousException(){
        $testExecutionId = 'myTestId';
        $compilationId = 'myCompilationId';

        $this->testRunnerService->expects($this->any())
            ->method('getItemData')
            ->with($compilationId)
            ->willReturn(array('myFirstKey', 'mySecondKey'));

        $this->testRunnerService->expects($this->any())
            ->method('getPrevious')
            ->with($compilationId)
            ->willReturn(true);


        $execution = TestExecutionState::initNew($testExecutionId, $compilationId);

        $execution->previous();
    }

    public function testToString(){
        $testExecutionId = 'myTestId';
        $compilationId = 'myCompilationId';

        $this->testRunnerService->expects($this->once())
            ->method('getItemData')
            ->with($compilationId)
            ->willReturn(array('myFirstKey'));

        $execution = TestExecutionState::initNew($testExecutionId, $compilationId);
        $string = json_encode(array(
                'testExecutionId' => $testExecutionId,
                'compilationId' => $compilationId,
                'current' => 0,
                'itemExecutions' => array(
                                        array(
                                            'itemIndex' => 0,
                                            'callId' => $testExecutionId.'_0',
                                        )
                                    )
            ));
        $this->assertEquals($string, $execution->toString());
    }

}
 