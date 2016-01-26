<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * This class tests the state of the ontology
 *
 * @author Joel Bout, <taosupport@tudor.lu>
 * @package tao
 
 */
class CardinalityTest extends TaoPhpUnitTestRunner {

	/**
	 * tests initialization
	 */
	public function setUp(){
		$this->markTestIncomplete('wfEngine still have multiple range, need to be solved');
		TaoPhpUnitTestRunner::initTest();
		
	}

	public function testProperties(){
	    
	    
        $propClass = new core_kernel_classes_Class(RDF_PROPERTY);
        foreach ($propClass->getInstances(true) as $property) {
            // invalid
            $widgets = $property->getPropertyValues(new core_kernel_classes_Property(PROPERTY_WIDGET));
            $this->assertTrue(count($widgets) <= 1, 'Property '.$property->getUri().' has several widgets assigned');
            
            // valid but not supported
            $domains = $property->getPropertyValues(new core_kernel_classes_Property(RDFS_DOMAIN));
            $this->assertTrue(count($domains) <= 1, 'Property '.$property->getUri().' has several domains assigned');
            
            // valid but not supported
            $ranges = $property->getPropertyValues(new core_kernel_classes_Property(RDFS_RANGE));
            $this->assertTrue(count($ranges) <= 1, 'Property '.$property->getUri().' has several ranges assigned');
        }
	}
	
	/**
	 * Test the service factory: dynamical instantiation and single instance serving
	 */
	public function testMultiple(){
	    $propClass = new core_kernel_classes_Class(RDF_PROPERTY);
	    $q = "SELECT subject, count(object)
                FROM statements
                    WHERE predicate = ?
                    GROUP BY subject
                    HAVING (count(object) > 1)";
	    foreach ($propClass->getInstances(true) as $property) {
            $property = new core_kernel_classes_Property($property);
            if (!$property->isMultiple() && !$property->isLgDependent()) {
                // bypass generis
                $result = core_kernel_classes_DbWrapper::singleton()->query($q, array($property->getUri()));
                while($statement = $result->fetch()){
                    $this->fail($property->getUri().' has multiple values but is not multiple.');
                }
            }
	    }
	}
	
}