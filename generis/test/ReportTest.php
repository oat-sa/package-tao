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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

use oat\generis\test\GenerisPhpUnitTestRunner;

class ReportTest extends GenerisPhpUnitTestRunner {
	
	public function testBasicReport()
	{
	    $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, 'test message');
	    $this->assertFalse($report->hasChildren());
	    $this->assertEquals('test message', (string)$report);
	    $this->assertEquals('test message', $report->getMessage());	    
	    $this->assertEquals(common_report_Report::TYPE_SUCCESS, $report->getType());
	    foreach ($report as $child) {
	        $this->fail('Should not contain children');
	    }
	}
	
	public function testDataInReport()
	{
	    $exception = new Exception('testing');
	    $report = new common_report_Report(common_report_Report::TYPE_INFO, 'test message2', $exception);
	    $this->assertFalse($report->hasChildren());
	    $this->assertEquals('test message2', (string)$report);
	    $this->assertEquals(common_report_Report::TYPE_INFO, $report->getType());
	    foreach ($report as $child) {
	        $this->fail('Should not contain children');
	    }
	    $this->assertSame($exception, $report->getData());
	}
   
	public function testNestedReport()
	{
	    $report = new common_report_Report(common_report_Report::TYPE_WARNING, 'test message3');
	    $sub1 = new common_report_Report(common_report_Report::TYPE_INFO, 'info31');
	    $sub2 = new common_report_Report(common_report_Report::TYPE_ERROR, 'error31');
	    $report->add(array($sub1, $sub2));
	    
	    $this->assertTrue($report->hasChildren());
	    $this->assertEquals('test message3', (string)$report);
	    $this->assertEquals(common_report_Report::TYPE_WARNING, $report->getType());
	    $array = array();
	    foreach ($report as $child) {
	        $array[] = $child;
	    }
	    $this->assertEquals(2, count($array));
	    list($first, $second) = $array;
	    
	    $this->assertFalse($first->hasChildren());
	    $this->assertEquals('info31', (string)$first);
	    $this->assertEquals(common_report_Report::TYPE_INFO, $first->getType());
	    foreach ($first as $child) {
	        $this->fail('Should not contain children');
	    }
	    
	    $this->assertFalse($second->hasChildren());
	    $this->assertEquals('error31', (string)$second);
	    $this->assertEquals(common_report_Report::TYPE_ERROR, $second->getType());
	    foreach ($second as $child) {
	        $this->fail('Should not contain children');
	    }

        $this->assertFalse($report->contains(common_report_Report::TYPE_SUCCESS));
        $this->assertTrue($report->contains(common_report_Report::TYPE_INFO));
	    $this->assertTrue($report->contains(common_report_Report::TYPE_ERROR));
	}
}