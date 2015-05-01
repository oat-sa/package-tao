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
 * 
 */

namespace oat\taoQtiItem\test;

use DOMDocument;
use DOMException;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\qti\Parser;

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoQTI

 */
class QtiOutputTest extends TaoPhpUnitTestRunner
{

    protected $qtiService;
    protected $validateOnParse;

    /**
     * tests initialization
     */
    public function setUp(){
        TaoPhpUnitTestRunner::initTest();
    }

    /**
     * test the building and exporting out the items
     * @dataProvider itemProvider
     */
    public function testToQTI($file){

        $qtiParser = new Parser($file);
        $item = $qtiParser->load();

        //test if content has been exported
        $qti = $item->toXML();
        $this->assertFalse(empty($qti));

        //test if it's a valid QTI file
        $tmpFile = $this->createFile('', uniqid('qti_', true).'.xml');
        file_put_contents($tmpFile, $qti);
        $this->assertTrue(file_exists($tmpFile));

        $parserValidator = new Parser($tmpFile);
        $parserValidator->validate();

        if(!$parserValidator->isValid()){
            $this->fail($file.' output invalid :'.$parserValidator->displayErrors().' -> '.$qti);
        }
    }

    /**
     * test the building and exporting out the items
     * @dataProvider itemProvider
     */
    public function testToXHTML($file){

        $doc = new DOMDocument();
        $doc->validateOnParse = true;

        $qtiParser = new Parser($file);
        $item = $qtiParser->load();

        $this->assertTrue($qtiParser->isValid());
        $this->assertNotNull($item);
        $this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\Item', $item);

        //test if content has been exported
        $xhtml = $item->toXHTML();
        $this->assertFalse(empty($xhtml));

        try{
            $doc->loadHTML($xhtml);
        }catch(DOMException $de){
            $this->fail($de);
        }
    }

    /**
     * test the building and exporting out the items
     * @dataProvider rpItemProvider
     */
    public function testResponseProcessingToArray($name, $file, $expectation){

        $qtiParser = new Parser($file);
        $item = $qtiParser->load();
        $this->assertTrue($qtiParser->isValid());
        $this->assertNotNull($item);
        $this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\Item', $item);

        $rp = $item->getResponseProcessing();
        $data = $rp->toArray();
        $this->assertFalse(empty($data));
        $this->assertFalse(empty($data['responseRules']));

        //compare the result with expectation
        $responseRules = json_decode($expectation, true);
        $this->assertEquals($data['responseRules'], $responseRules);
    }

    /**
     * 
     * @return multitype:
     */
    public function itemProvider(){
        $items = array();
        foreach(array_merge(glob(dirname(__FILE__).'/samples/xml/qtiv2p1/*.xml')) as $file){
            $items[] = array($file);
        }
        return $items;
    }

    /**
     * 
     * @return multitype
     */
    public function rpItemProvider(){
        $sampleDirectory = dirname(__FILE__).'/samples/xml/qtiv2p1/';
        return array(
            array(
                'name' => 'custom',
                'file' => $sampleDirectory.'order_partial_scoring.xml',
                'expectation' => '[{"qtiClass":"responseCondition","responseIf":{"qtiClass":"responseIf","expression":{"qtiClass":"match","expressions":[{"qtiClass":"variable","attributes":{"identifier":"RESPONSE"}},{"qtiClass":"correct","attributes":{"identifier":"RESPONSE"}}]},"responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"SCORE"},"expression":{"qtiClass":"baseValue","attributes":{"baseType":"float"},"value":"2"}}]},"responseElseIfs":[{"qtiClass":"responseElseIf","expression":{"qtiClass":"match","expressions":[{"qtiClass":"variable","attributes":{"identifier":"RESPONSE"}},{"qtiClass":"ordered","expressions":[{"qtiClass":"baseValue","attributes":{"baseType":"identifier"},"value":"DriverC"},{"qtiClass":"baseValue","attributes":{"baseType":"identifier"},"value":"DriverB"},{"qtiClass":"baseValue","attributes":{"baseType":"identifier"},"value":"DriverA"}]}]},"responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"SCORE"},"expression":{"qtiClass":"baseValue","attributes":{"baseType":"float"},"value":"1"}}]}],"responseElse":{"qtiClass":"responseElse","responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"SCORE"},"expression":{"qtiClass":"baseValue","attributes":{"baseType":"float"},"value":"0"}}]}}]'
            ),
            array(
                'name' => 'template',
                'file' => $sampleDirectory.'associate.xml',
                'expectation' => '[{"qtiClass":"responseCondition","responseIf":{"qtiClass":"responseIf","expression":{"qtiClass":"isNull","expressions":[{"qtiClass":"variable","attributes":{"identifier":"RESPONSE"}}]},"responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"SCORE"},"expression":{"qtiClass":"baseValue","attributes":{"baseType":"float"},"value":"0.0"}}]},"responseElse":{"qtiClass":"responseElse","responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"SCORE"},"expression":{"qtiClass":"mapResponse","attributes":{"identifier":"RESPONSE"}}}]}}]'
            ),
            array(
                'name' => 'composite',
                'file' => $sampleDirectory.'composite.xml',
                'expectation' => '[{"qtiClass":"responseCondition","responseIf":{"qtiClass":"responseIf","expression":{"qtiClass":"isNull","expressions":[{"qtiClass":"variable","attributes":{"identifier":"RESPONSE"}}]},"responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"outcome_2"},"expression":{"qtiClass":"baseValue","attributes":{"baseType":"string"},"value":"0"}}]}},{"qtiClass":"responseCondition","responseIf":{"qtiClass":"responseIf","expression":{"qtiClass":"match","expressions":[{"qtiClass":"variable","attributes":{"identifier":"response_1"}},{"qtiClass":"correct","attributes":{"identifier":"response_1"}}]},"responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"outcome_1"},"expression":{"qtiClass":"baseValue","attributes":{"baseType":"integer"},"value":"1"}}]}},{"qtiClass":"setOutcomeValue","attributes":{"identifier":"SCORE"},"expression":{"qtiClass":"sum","expressions":[{"qtiClass":"variable","attributes":{"identifier":"outcome_2"}},{"qtiClass":"variable","attributes":{"identifier":"outcome_1"}}]}}]'
            ),
            array(
                'name' => 'composite_complex',
                'file' => $sampleDirectory.'composite_complex_rp.xml',
                'expectation' => '[{"qtiClass":"responseCondition","responseIf":{"qtiClass":"responseIf","expression":{"qtiClass":"isNull","expressions":[{"qtiClass":"variable","attributes":{"identifier":"RESPONSE"}}]},"responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"outcome_2"},"expression":{"qtiClass":"baseValue","attributes":{"baseType":"string"},"value":"0"}}]}},{"qtiClass":"responseCondition","responseIf":{"qtiClass":"responseIf","expression":{"qtiClass":"match","expressions":[{"qtiClass":"variable","attributes":{"identifier":"response_1"}},{"qtiClass":"correct","attributes":{"identifier":"response_1"}}]},"responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"outcome_1"},"expression":{"qtiClass":"baseValue","attributes":{"baseType":"integer"},"value":"1"}}]},"responseElseIfs":[{"qtiClass":"responseElseIf","expression":{"qtiClass":"match","expressions":[{"qtiClass":"variable","attributes":{"identifier":"response_1"}},{"qtiClass":"baseValue","attributes":{"baseType":"integer"},"value":"0"}]},"responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"REVEALED"},"expression":{"qtiClass":"random","expressions":[{"qtiClass":"multiple","expressions":[{"qtiClass":"baseValue","attributes":{"baseType":"integer"},"value":"1"},{"qtiClass":"baseValue","attributes":{"baseType":"integer"},"value":"2"}]}]}}]},{"qtiClass":"responseElseIf","expression":{"qtiClass":"match","expressions":[{"qtiClass":"variable","attributes":{"identifier":"response_1"}},{"qtiClass":"baseValue","attributes":{"baseType":"integer"},"value":"2"}]},"responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"REVEALED"},"expression":{"qtiClass":"random","expressions":[{"qtiClass":"multiple","expressions":[{"qtiClass":"baseValue","attributes":{"baseType":"integer"},"value":"3"},{"qtiClass":"baseValue","attributes":{"baseType":"integer"},"value":"4"}]}]}}]}],"responseElse":{"qtiClass":"responseElse","responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"REVEALED"},"expression":{"qtiClass":"random","expressions":[{"qtiClass":"multiple","expressions":[{"qtiClass":"baseValue","attributes":{"baseType":"integer"},"value":"5"},{"qtiClass":"baseValue","attributes":{"baseType":"integer"},"value":"6"}]}]}}]}},{"qtiClass":"setOutcomeValue","attributes":{"identifier":"SCORE"},"expression":{"qtiClass":"sum","expressions":[{"qtiClass":"variable","attributes":{"identifier":"outcome_2"}},{"qtiClass":"variable","attributes":{"identifier":"outcome_1"}}]}},{"qtiClass":"responseProcessingFragment","responseRules":[{"qtiClass":"setOutcomeValue","attributes":{"identifier":"SCORE_0"},"expression":{"qtiClass":"multiple","expressions":[{"qtiClass":"variable","attributes":{"identifier":"SCORE"}},{"qtiClass":"baseValue","attributes":{"baseType":"string"},"value":"2"}]}},{"qtiClass":"setOutcomeValue","attributes":{"identifier":"SCORE_1"},"expression":{"qtiClass":"divide","expressions":[{"qtiClass":"variable","attributes":{"identifier":"SCORE"}},{"qtiClass":"baseValue","attributes":{"baseType":"string"},"value":"3"}]}},{"qtiClass":"setOutcomeValue","attributes":{"identifier":"SCORE_3"},"expression":{"qtiClass":"sum","expressions":[{"qtiClass":"variable","attributes":{"identifier":"SCORE_0"}},{"qtiClass":"variable","attributes":{"identifier":"SCORE_1"}}]}}]}]'
            )
        );
    }

}