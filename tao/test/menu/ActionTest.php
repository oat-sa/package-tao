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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @license GPLv2
 * @package tao
 *
 */

namespace oat\tao\menu\test;

use oat\tao\model\menu\Action;
use oat\tao\model\menu\Icon;
use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

/**
 * Unit test the  oat\tao\model\menu\Action 
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package tao
 */
class ActionTest extends TaoPhpUnitTestRunner {
    
    /**
     * Data Provider : xml and a mock action that the xml should produce
     * @return array the data
     */ 
    public function actionsProvider(){
        return array(
            array('<action id="search" name="search" url="/tao/Roles/search" context="*" group="content">
                        <icon id="search" src="images/icon.svg"/>
                    </action>',
                  new Action(array(
                        'id'      => 'search',
                        'name'      => 'search',
                        'url'       => '/tao/Roles/search',
                        'context'   => '*',
                        'group'     => 'content',
                        'binding'   => 'load',
                        'reload'    => false,
                        'disabled'    => false,
                        'icon'      => new Icon(array( 'id' => 'search', 'src' => 'images/icon.svg', 'ext' => '')),
                        'extension' => 'tao',
                        'controller' => 'Roles',
                        'action' => 'search'
                  )) 
            ),
            array('<action id="delete" name="delete" js="removeNode" url="/tao/Roles/delete" context="resource" disabled="true"/>',
                  new Action(array(
                        'id'      => 'delete',
                        'name'      => 'delete',
                        'url'       => '/tao/Roles/delete',
                        'context'   => 'resource',
                        'group'     => 'tree',
                        'binding'   => 'removeNode',
                        'reload'    => false,
                        'disabled'    => true,
                        'icon'      => new Icon(array('id' => null, 'src' => 'actions/delete.png', 'ext' => 'tao')),
                        'extension' => 'tao',
                        'controller' => 'Roles',
                        'action' => 'delete'
                  )) 
            )
        );
    }
    
    /**
     * Test the xml produce the same object than the one expected by the provider. 
     * 
     * @dataProvider actionsProvider
     * 
     * @param string $xml the action node 
     * @param Action $expected the expected object to be produced
     */
    public function testActions($xml, $expected){
        $result = Action::fromSimpleXMLElement(new \SimpleXMLElement($xml));
    
        $this->assertTrue($result instanceof Action);        
        $this->assertEquals($expected, $result);
    }

}
