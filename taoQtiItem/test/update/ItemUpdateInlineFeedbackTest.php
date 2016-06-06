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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 *
 */

namespace oat\taoQtiItem\test\update;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\update\ItemUpdateInlineFeedback;
use \tao_helpers_File;

class ItemUpdateInlineFeedbackTest extends TaoPhpUnitTestRunner
{

    /**
     * tests initialization
     * load qti service
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');//load tao constants to prevent warning in constant usage
    }

    public function testUpdate()
    {
        $itemRootDir = realpath(ROOT_PATH.'taoQtiItem/test/update/samples/itemData');
        $itemUpdater   = new ItemUpdateInlineFeedback($itemRootDir);
        $items = $itemUpdater->update();
        $checkedFiles  = $itemUpdater->getCheckedFiles();
        $modifiedFiles = array_keys(array_filter($checkedFiles, function($v) {
                return $v;
            }));
            
        $this->assertEquals(19, count($checkedFiles));
        $this->assertEquals(2, count($modifiedFiles));

        sort($modifiedFiles);
        $this->assertEquals($modifiedFiles[0], realpath($itemRootDir.'/i1452699358831159_hasModal_willChange/itemContent/en-US/qti.xml'));
        $this->assertEquals($modifiedFiles[1], realpath($itemRootDir.'/i1452759848383063_hasModal_willChange/itemContent/en-US/qti.xml'));

        $item1 = $items[$modifiedFiles[0]];
        $item2 = $items[$modifiedFiles[1]];

        $this->assertTrue($item1 instanceof \oat\taoQtiItem\model\qti\Item);
        $this->assertTrue($item2 instanceof \oat\taoQtiItem\model\qti\Item);

        $itemStr1 = $this->normalizeXmlStrings($item1->toXML());
        $itemStr2 = $this->normalizeXmlStrings($item2->toXML());//note : item2 is clone of item1

        //compare the content of the items after update
        $resultFile = dirname(__FILE__).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR.'updateResult.xml';
        $resultFileContent = file_get_contents($resultFile);
        $resultFileContent = $this->normalizeXmlStrings($resultFileContent);
        $this->assertEquals($resultFileContent, $itemStr1);
        $this->assertEquals($resultFileContent, $itemStr2);
    }

    public function testUpdateTrue()
    {
        $itemRootDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid('ItemUpdateInlineFeedbackTest').DIRECTORY_SEPARATOR;
        $originalRootDir = realpath(ROOT_PATH.'taoQtiItem/test/update/samples/itemData');
        tao_helpers_File::copy($originalRootDir, $itemRootDir);

        $itemUpdater   = new ItemUpdateInlineFeedback($itemRootDir);
        //test file modification
        $itemUpdater->update(true);
        $checkedFiles  = $itemUpdater->getCheckedFiles();
        $modifiedFiles = array_keys(array_filter($checkedFiles, function($v) {
                return $v;
            }));

        $this->assertEquals(19, count($checkedFiles));
        $this->assertEquals(2, count($modifiedFiles));
        
        sort($modifiedFiles);
        $this->assertEquals($modifiedFiles[0], realpath($itemRootDir.'/i1452699358831159_hasModal_willChange/itemContent/en-US/qti.xml'));
        $this->assertEquals($modifiedFiles[1], realpath($itemRootDir.'/i1452759848383063_hasModal_willChange/itemContent/en-US/qti.xml'));

        //compare the content of the items after update
        $resultFile = dirname(__FILE__).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR.'updateResult.xml';
        $resultFileContent = file_get_contents($resultFile);
        $resultFileContent = $this->normalizeXmlStrings($resultFileContent);
        $this->assertEquals($resultFileContent, $this->normalizeXmlStrings(file_get_contents($modifiedFiles[0])));
        $this->assertEquals($resultFileContent, $this->normalizeXmlStrings(file_get_contents($modifiedFiles[1])));
    }

    private function normalizeXmlStrings($s) {
        $s = preg_replace('/\stoolVersion="[0-9\.]*-sprint[0-9]*"/', '', $s);
        // Normalize line endings
        // Convert all line-endings to UNIX format
        $s = str_replace("\r\n", "\n", $s);
        $s = str_replace("\r", "\n", $s);
        // Don't allow out-of-control blank lines
        $s = preg_replace("/\n{2,}/", "\n\n", $s);

        $s=trim($s);
        return $s;
    }
}