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
 */

namespace oat\generis\test\model;

use \core_kernel_classes_ResourceFormatter;
use oat\generis\test\GenerisPhpUnitTestRunner;
use Prophecy\Prophet;


class ResourceFormatterTest extends GenerisPhpUnitTestRunner
{
    /**
     * 
     * @var Prophet
     */
    private $prophet;
    /**
     * 
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
        $this->prophet = new Prophet();
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $uri
     */
    private function createPropertyProphecy($uri)
    {
        $propertyProphecy = $this->createResourceProphecy($uri);
        $propertyProphecy->__toString()->willReturn($uri);
        return $propertyProphecy;
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $uri
     */
    private function createResourceProphecy($uri){
        $resourceProphecy = $this->prophet->prophesize('core_kernel_classes_Resource');
        $resourceProphecy->getUri()->willReturn($uri);
        return $resourceProphecy;
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $uri
     */
    private function createClassProphecy($uri){
        $classProphecy = $this->prophet->prophesize('core_kernel_classes_Class');
        $classProphecy->getUri()->willReturn($uri);
        return $classProphecy;
    }
    /**
     * 
     * Create a mock to test formater result
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $withNoValue
     * @return Prophecy/Double
     */
    private function createResourceDescription($withNoValue = false)
    {
        $resourceDescProphecy = $this->createResourceProphecy('#fakeUri');
        $propertyProphecy = $this->createPropertyProphecy('#propertyUri');
        $propertyProphecy2 = $this->createPropertyProphecy('#propertyUri2');
        
        $typeProphecy = $this->createClassProphecy('#typeUri');
        $typeProphecy->getProperties(true)->willReturn(
            array(
                $propertyProphecy->reveal(),
                $propertyProphecy2->reveal()
            )
        );
        
        $typeProphecy2 = $typeProphecy = $this->createClassProphecy('#typeUri2');
        $typeProphecy->getProperties(true)->willReturn(array());
        
        $prop1 = $propertyProphecy->reveal();
        $prop2 = $propertyProphecy2->reveal();
      
        $typeProphecy2->getProperties(true)->willReturn(
            array(
                $prop1,
                $prop2
            )
        );
        $resourceDescProphecy->getTypes()->willReturn(
            array(
                $typeProphecy->reveal(),
                $typeProphecy2->reveal()
            )
        );
        if($withNoValue) {
            $resourceDescProphecy->getPropertiesValues(
                array(
                    "#propertyUri" => $prop1,
                    "#propertyUri2" => $prop2
                )
            )->willReturn(array());
            
        } else {
            $resourceDescProphecy->getPropertiesValues(array(
                "#propertyUri" => $prop1,
                "#propertyUri2" => $prop2
            ))->willReturn(array(
                '#propertyUri' => array(
                    new \core_kernel_classes_Literal('value1'),
                    new \core_kernel_classes_Literal('value2')
                ),
                '#propertyUri2' => array(
                    new \core_kernel_classes_Resource(GENERIS_BOOLEAN)
                )
            ));
        }
        return $resourceDescProphecy->reveal();
    }
    
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetResourceDescriptionNoContent()
    {
        $formatter = new core_kernel_classes_ResourceFormatter();
        try {
            $result = $formatter->getResourceDescription($this->createResourceDescription(true));
            $this->fail('common_exception_NoContent should have been raised');
        } catch (\Exception $e) {
            $this->assertInstanceOf('common_exception_NoContent', $e);
        }
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetResourceDesciptionFromDef()
    {
        $formatter = new core_kernel_classes_ResourceFormatter();
        $result =$formatter->getResourceDescription($this->createResourceDescription(false));
         
         $this->assertInstanceOf('stdClass', $result);
         $this->assertAttributeEquals('#fakeUri', 'uri', $result);         
         $this->assertAttributeInternalType('array', 'properties', $result);
         $this->assertAttributeCount(2, 'properties', $result);
         
         $this->assertInstanceOf('stdClass', $result->properties[0]);
         $this->assertAttributeEquals('#propertyUri', 'predicateUri', $result->properties[0]);
         $this->assertAttributeInternalType('array', 'values', $result->properties[0]);
         $this->assertAttributeCount(2, 'values', $result->properties[0]);
         
         $this->assertInstanceOf('stdClass', $result->properties[0]->values[0]);
         $this->assertAttributeEquals('literal', 'valueType',  $result->properties[0]->values[0]);
         $this->assertAttributeEquals('value1', 'value',  $result->properties[0]->values[0]);
          
         $this->assertInstanceOf('stdClass', $result->properties[0]->values[1]);
         $this->assertAttributeEquals('literal', 'valueType',  $result->properties[0]->values[1]);
         $this->assertAttributeEquals('value2', 'value',  $result->properties[0]->values[1]);
         
         $this->assertInstanceOf('stdClass', $result->properties[1]);
         $this->assertAttributeEquals('#propertyUri2', 'predicateUri', $result->properties[1]);
         $this->assertAttributeInternalType('array', 'values', $result->properties[1]);
         $this->assertAttributeCount(1, 'values', $result->properties[1]);
         
         $this->assertInstanceOf('stdClass', $result->properties[1]->values[0]);
         $this->assertAttributeEquals('resource', 'valueType',  $result->properties[1]->values[0]);
         $this->assertAttributeEquals(GENERIS_BOOLEAN, 'value',  $result->properties[1]->values[0]);

    }
    
    /**
     * @expectedException common_exception_NoContent
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetResourceDesciptionNoContentTripple()
    {
        
        $resourceDescProphecy = $this->createResourceProphecy('#fakeUri');
        $resourceDescProphecy->getRdfTriples()->willReturn(array());
        $formatter = new core_kernel_classes_ResourceFormatter();
        
        $result =$formatter->getResourceDescription($resourceDescProphecy->reveal(),false);

    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return array
     */
    private function generateTriple(){
        $returnValue = array();
        for ( $i = 0; $i < 3;$i++ ){
            $triple = new \core_kernel_classes_Triple();
            $triple->subject = '#subject' . $i;
            $triple->predicate = '#predicate' . $i;
            $triple->object = $i==0 ? GENERIS_BOOLEAN : 'object' . $i;
            $returnValue[] = $triple; 
        }
        return $returnValue;
    }
    
    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetResourceDesciption()
    {
    
        $resourceDescProphecy = $this->createResourceProphecy('#fakeUri');
        
        $resourceDescProphecy->getRdfTriples()->willReturn($this->generateTriple());
        $formatter = new core_kernel_classes_ResourceFormatter();
    
        $result =$formatter->getResourceDescription($resourceDescProphecy->reveal(),false);
                
        $this->assertInstanceOf('stdClass', $result);
        $this->assertAttributeEquals('#fakeUri', 'uri', $result);         
        $this->assertAttributeInternalType('array', 'properties', $result);
        $this->assertAttributeCount(3, 'properties', $result);
        
        $this->assertInstanceOf('stdClass', $result->properties[0]);
        $this->assertAttributeEquals('#predicate0', 'predicateUri', $result->properties[0]);
        $this->assertAttributeInternalType('array', 'values', $result->properties[0]);
        $this->assertAttributeCount(1, 'values', $result->properties[0]);
        
        $this->assertInstanceOf('stdClass', $result->properties[0]->values[0]);
        $this->assertAttributeEquals('resource', 'valueType',  $result->properties[0]->values[0]);
        $this->assertAttributeEquals(GENERIS_BOOLEAN, 'value',  $result->properties[0]->values[0]);
        
        for ( $i = 1; $i < 3;$i++ ){
            $this->assertInstanceOf('stdClass', $result->properties[$i]);
            $this->assertAttributeEquals('#predicate'. $i, 'predicateUri', $result->properties[$i]);
            $this->assertAttributeInternalType('array', 'values', $result->properties[$i]);
            $this->assertAttributeCount(1, 'values', $result->properties[$i]);
            
            $this->assertInstanceOf('stdClass', $result->properties[$i]->values[0]);
            $this->assertAttributeEquals('literal', 'valueType',  $result->properties[$i]->values[0]);
            $this->assertAttributeEquals('object'.$i, 'value',  $result->properties[$i]->values[0]);
        }
    
    } 
        
}

?>