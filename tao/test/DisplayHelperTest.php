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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */


use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * PHPUnit test of the {@link tao_helpers_Duration} helper
 * @package tao
 
 */
class DisplayHelperTest extends TaoPhpUnitTestRunner {
    
    /**
     * Data provider for the testTimetoDuration method
     * @return array[] the parameters
     */
    public function stringToCleanProvider(){
        return array(
            array('This is a simple text',          '_', -1, 'This_is_a_simple_text'),
            array('This is a simple text',          '-', 10, 'This_is_a_'),
            array('@à|`',                           '-', -1, '----'),
            array('@à|`',                           '-',  2, '--'),
            array('This 4s @ \'stronger\' tèxte',   '',  -1, 'This_4s__stronger_txt')
        );
    }
    
    /**
     * Test {@link tao_helpers_Display::}
     * @dataProvider stringToCleanProvider
     */
    public function testCleaner($input, $joker, $maxLength, $output){
        $this->assertEquals(tao_helpers_Display::textCleaner($input, $joker, $maxLength), $output); 
    }
}
