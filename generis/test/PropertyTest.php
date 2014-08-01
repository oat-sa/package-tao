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
?>
<?php
require_once dirname(__FILE__) . '/GenerisPhpUnitTestRunner.php';


class PropertyTest extends GenerisPhpUnitTestRunner{
	
	protected $object;
	
	public function setUp(){
        GenerisPhpUnitTestRunner::initTest();
		$this->object = new core_kernel_classes_Property(PROPERTY_WIDGET);
	}
	
	public function testGetDomain(){
		$domainCollection = $this->object->getDomain();
		$this->assertTrue($domainCollection instanceof core_kernel_classes_ContainerCollection  );
		$domain = $domainCollection->get(0);
		$this->assertEquals($domain->getUri(),RDF_PROPERTY);
		$this->assertEquals($domain->getLabel(),'Property');
		$this->assertEquals($domain->getComment(),'The class of RDF properties.');
	}
	
	public function testGetRange(){
		$range = $this->object->getRange();
		$this->assertTrue($range instanceof core_kernel_classes_Class );
		$this->assertEquals($range->getUri(),CLASS_WIDGET);
		$this->assertEquals($range->getLabel(), 'Widget Class');
		$this->assertEquals($range->getComment(), 'The class of all possible widgets');
	}
	
	public function testGetWidget(){
		$widget = $this->object->getWidget();
		$this->assertTrue($widget instanceof core_kernel_classes_Resource );
		$this->assertEquals($widget->getUri(),WIDGET_COMBO);
		$this->assertEquals($widget->getLabel(), 'Drop down menu');
		$this->assertEquals($widget->getComment(), 'In drop down menu, one may select 1 to N options');
	}	

}
?>