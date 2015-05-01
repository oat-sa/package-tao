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

use oat\tao\model\menu\Section;
use oat\tao\model\menu\Tree;
use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

/**
 * Unit test the  oat\tao\model\menu\Section class 
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package tao
 */
class SectionTest extends TaoPhpUnitTestRunner {
    
    /**
     * Data Provider : provides xml, from legacy and new format, and also the expect result
     * @return array the data
     */ 
    public function legacyAndNewSectionProvider(){

        $sectionNew = <<<XML
<section id="manage_items" name="Manage items" url="/taoItems/Items/index">
    <trees>
        <tree name="Items library"
            className="Item"
            dataUrl="/taoItems/Items/getOntologyData"
            selectClass="edit class"
            selectInstance="edit item"
        />
    </trees>
    <actions>
        <action id="edit_class" name="edit class" url="/taoItems/Items/editItemClass" group="none" context="class" />
        <action id="edit_item" name="edit item"  url="/taoItems/Items/editItem"      group="none" context="instance" />
        <action id="new_class" name="new class" js="subClass" url="/taoItems/Items/addSubClass" context="class" />
        <action id="new_item" name="new item" js="instanciate" url="/taoItems/Items/addInstance" context="class" />
    </actions>
</section>
XML;

        $sectionLegacy = <<<XML
<section id="manage_items" name="Manage items" url="/taoItems/Items/index">
    <trees>
        <tree name="Items library"
              className="Item"
              dataUrl="/taoSubjects/Subjects/getOntologyData"
              editClassUrl="/taoItems/Items/editItemClass"
              editInstanceUrl="/taoItems/Items/editItem" />
    </trees>
    <actions>
        <action id="new_class" name="new class" js="subClass" url="/taoItems/Items/addSubClass" context="class" />
        <action id="new_item" name="new item" js="instanciate" url="/taoItems/Items/addInstance" context="class" />
    </actions>
</section>
XML;
        return array(
            array($sectionNew, $sectionLegacy)
        );
    }
    
    /**
     * Test the section can be loaded from either legacy or new XML format 
     * 
     * @dataProvider legacyAndNewSectionProvider
     * 
     * @param string $xml new format xml
     * @param string $xml legacy format xml
     */
    public function testActions($newXml, $legacyXml){
        $sectionFromNew = Section::fromSimpleXMLElement(new SimpleXMLElement($newXml));

        $this->assertTrue($sectionFromNew instanceof Section);
        $this->assertEquals(count($sectionFromNew->getActions()), 4);


        $sectionFromLegacy = Section::fromSimpleXMLElement(new SimpleXMLElement($legacyXml));
    
        $this->assertTrue($sectionFromLegacy instanceof Section);
        $this->assertEquals(count($sectionFromLegacy->getActions()), 4);
        $this->assertEquals(count($sectionFromLegacy->getTrees()), 1);

        $trees =  $sectionFromLegacy->getTrees();
        $tree  =  $trees[0];

        $this->assertTrue($tree instanceof Tree);
        $this->assertFalse(is_null($tree->get('selectClass')));
        $this->assertEquals('edit_class', $tree->get('selectClass'));
    }

}
