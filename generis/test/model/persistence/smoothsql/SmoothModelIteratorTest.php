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
 * Copyright (c) (original work) 2015 Open Assessment Technologies SA
 * 
 */
namespace oat\generis\test\model\persistence\smoothsql;

use oat\generis\test\GenerisPhpUnitTestRunner;
use Prophecy\Promise\ReturnPromise;
use Prophecy\Argument;

class SmoothModelIteratorTest extends GenerisPhpUnitTestRunner
{

    public function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
    }

   
    private function createIterator()
    {

        $persistenceProphecy = $this->prophesize('common_persistence_SqlPersistence');
        
        $statementProphecy = $this->prophesize('PDOStatement');
        $statementValue = array(
            "modelid" => 1,
            "subject" => '#subject',
            "predicate" => '#predicate',
            "object" => 'obb',
            "id" => 898,
            "l_language" => 'en-US'
        );
        $statementValue2 = array(
            "modelid" => 1,
            "subject" => '#subject2',
            "predicate" => '#predicate2',
            "object" => 'ob2',
            "id" =>899,
            "l_language" => 'en-US'
        
        );
        $return = new ReturnPromise(array(
            $statementValue,
            $statementValue2,
            false
        ));
        
        $statementProphecy->fetch()->will($return);
        
        $plop = $statementProphecy->reveal();
        $persistenceProphecy
        ->query('SELECT * FROM statements WHERE id > ? ORDER BY id LIMIT ?', Argument::type('array'))
        ->willReturn($plop);
         
        $iterator = new \core_kernel_persistence_smoothsql_SmoothIterator($persistenceProphecy->reveal());
        return $iterator;
    }
    
    public function testConstruct()
    {
        $this->assertInstanceOf('core_kernel_persistence_smoothsql_SmoothIterator', $this->createIterator());
    }
    
    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testCurrent()
    {
                
        $iterator = $this->createIterator();
        $this->assertTrue($iterator->valid());
        
        $current = $iterator->current();
        $this->assertInstanceOf('core_kernel_classes_Triple',$current);
        $this->assertAttributeEquals(1,'modelid', $current);
        $this->assertAttributeEquals('#subject','subject', $current);
        $this->assertAttributeEquals('#predicate','predicate', $current);
        $this->assertAttributeEquals('obb','object', $current);
        $this->assertAttributeEquals(898,'id', $current);
        $this->assertAttributeEquals('en-US','lg', $current);

    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testNext()
    {
        $iterator = $this->createIterator();
        $iterator->next();
        $this->assertTrue($iterator->valid());
        $current = $iterator->current();
        $this->assertInstanceOf('core_kernel_classes_Triple',$current);
        $this->assertAttributeEquals(1,'modelid', $current);
        $this->assertAttributeEquals('#subject2','subject', $current);
        $this->assertAttributeEquals('#predicate2','predicate', $current);
        $this->assertAttributeEquals('ob2','object', $current);
        $this->assertAttributeEquals(899,'id', $current);
        $this->assertAttributeEquals('en-US','lg', $current);
        
    }
    

}

?>