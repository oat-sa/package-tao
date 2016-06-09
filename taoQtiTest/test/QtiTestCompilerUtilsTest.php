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
 */
namespace oat\taoQtiTest\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use \taoQtiTest_helpers_TestCompilerUtils;
use \qtism\data\storage\xml\XmlDocument;


/**
 * This test case focuses on testing the TestCompilerUtils helper.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @package taoQtiTest
 * 
 */
class QtiTestCompilerUtilsTest extends TaoPhpUnitTestRunner {
    
    static public function samplesDir() {
        return dirname(__FILE__) . '/samples/xml/compiler/meta/';
    }
    
    /**
     * 
     * @dataProvider metaProvider
     * @param string $testFile
     * @param array $expectedMeta
     */
    public function testTestMeta($testFile, $expectedMeta) {
        $xml = new XmlDocument();
        $xml->load($testFile);
        
        $this->assertEquals($expectedMeta, taoQtiTest_helpers_TestCompilerUtils::testMeta($xml->getDocumentComponent()));
    }
    
    public function metaProvider() {
        return array(
            array(self::samplesDir() . 'linear_nopreconditions_nobranchrules.xml', array('branchRules' => false, 'preConditions' => false)),
            array(self::samplesDir() . 'linear_preconditions_nobranchrules.xml', array('branchRules' => false, 'preConditions' => true)),
            array(self::samplesDir() . 'linear_nopreconditions_branchrules.xml', array('branchRules' => true, 'preConditions' => false)),
            array(self::samplesDir() . 'linear_preconditions_branchrules.xml', array('branchRules' => true, 'preConditions' => true)),
            array(self::samplesDir() . 'nonlinear_nopreconditions_nobranchrules.xml', array('branchRules' => false, 'preConditions' => false)),
            array(self::samplesDir() . 'nonlinear_nopreconditions_branchrules.xml', array('branchRules' => false, 'preConditions' => false)),
            array(self::samplesDir() . 'nonlinear_preconditions_branchrules.xml', array('branchRules' => false, 'preConditions' => false)),
            array(self::samplesDir() . 'nonlinear_preconditions_nobranchrules.xml', array('branchRules' => false, 'preConditions' => false)),
        );
    }
}