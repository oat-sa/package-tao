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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 */

namespace oat\taoQtiItem\test\metadata;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\qti\metadata\imsManifest\ImsManifestMetadataExtractor;
use \DOMDocument;
use \stdClass;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class ImsManifestExtractionTest extends TaoPhpUnitTestRunner
{
    protected $imsManifestExtractor;
    
    public function setUp()
    {
        parent::setUp();
        $this->imsManifestExtractor = new ImsManifestMetadataExtractor();
    }
    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->imsManifestExtractor);
    }
    
    /**
     * @dataProvider sampleProvider
     * 
     * @param string $imsManifestFile The relative path to the sample IMS Manifest File e.g. "sample1.xml".
     * @param string $key The key of the returned metadata value array where to find the MetadataValue objects that belong to a given Resource Identifier.
     * @param integer $index The index where to find the metadata value. The index begins at 0.
     * @param string $path The path of the metadata.
     * @param string $identifier A QTI identifier.
     * @param string $type A QTI resource type.
     * @param string $href An hyperext reference.
     * @param string $val A metadata intrinsic value.
     * @param string $lang (optional) A language value.
     */
    public function testSample($imsManifestFile, $key, $index, $path, $identifier, $type, $href, $val, $lang = '')
    {
        $domManifest = new DOMDocument('1.0', 'UTF-8');
        $domManifest->load(dirname(__FILE__) . '/../samples/metadata/imsManifestExtraction/' . $imsManifestFile);
        
        $values = $this->imsManifestExtractor->extract($domManifest);
        
        $this->assertTrue(isset($values[$key]), "No metadata array found at key '${key}'.");
        $this->assertTrue(isset($values[$key][$index]), "No Metadata value found at index '${index}' for key '${key}' in file '${imsManifestFile}'.");
        
        $value = $values[$key][$index];
        $this->assertInstanceOf(
            'oat\\taoQtiItem\\model\\qti\\metadata\\MetadataValue',
            $value,
            "The value found at index '${index}' is not a MetadataValue object in file '${imsManifestFile}'."
        );
        
        $this->assertEquals($path, $value->getPath(), "The MetadataValue object with index '${index}' contains an unexpected Path in file '${imsManifestFile}'.");
        $this->assertEquals($identifier, $value->getResourceIdentifier(), "The MetadataValue object with index '${index}' contains an unexpected Resource Identifier in file '${imsManifestFile}'.");
        $this->assertEquals($type, $value->getResourceType(), "The MetadataValue object with index '${index}' contains an unexpected Resource Type in file '${imsManifestFile}'.");
        $this->assertEquals($href, $value->getResourceHref(), "The MetadataValue object with index '${index}' contains an unexpected Resource Hypertext reference in file '${imsManifestFile}'.");
        $this->assertEquals($val, $value->getValue(), "The MetadataValue object with index '${index}' contains an unexpected intrinsic metadata value in file '${imsManifestFile}'.");
        $this->assertEquals($lang, $value->getLanguage(), "The MetadataValue object with index '${index}' contains an unexpected language in file '${imsManifestFile}'.");
    }
    
    public function sampleProvider()
    {
        return array(
            // -- Sample #1
            array(
                'sample1.xml',
                'choice',
                0,
                array(
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'qti_v2_item_01'
            ),
            array(
                'sample1.xml',
                'choice',
                1,
                array(
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#title',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#langstring'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'Metadata Example Item #1',
                'en'
            ),
            array(
                'sample1.xml',
                'choice',
                2,
                array(
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#description',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#langstring'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'This is a dummy item',
                'en'
            ),
            array(
                'sample1.xml',
                'choice',
                3,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#timeDependent'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'false'
            ),
            array(
                'sample1.xml',
                'choice',
                4,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#interactionType'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'choiceInteraction'
            ),
            array(
                'sample1.xml',
                'choice',
                5,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#feedbackType'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'nonadaptive'
            ),
            array(
                'sample1.xml',
                'choice',
                6,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#solutionAvailable'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'true'
            ),
            array(
                'sample1.xml',
                'choice',
                7,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#toolName'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'XMLSPY'
            ),
            array(
                'sample1.xml',
                'choice',
                8,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#toolVersion'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                '5.4'
            ),
            array(
                'sample1.xml',
                'choice',
                9,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#toolVendor'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'ALTOVA'
            ),
                        
            // -- Sample #2.
            array(
                'sample2.xml',
                'choice',
                0,
                array(
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'qti_v2_item_01'
            ),
            array(
                'sample2.xml',
                'choice',
                1,
                array(
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#title',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#langstring'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'Metadata Example Item #1',
                'en'
            ),
            array(
                'sample2.xml',
                'choice',
                2,
                array(
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#description',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#langstring'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'This is a dummy item',
                'en'
            ),
            array(
                'sample2.xml',
                'choice',
                3,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#timeDependent'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'false'
            ),
            array(
                'sample2.xml',
                'choice',
                4,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#interactionType'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'choiceInteraction'
            ),
            array(
                'sample2.xml',
                'choice',
                5,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#feedbackType'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'nonadaptive'
            ),
            array(
                'sample2.xml',
                'choice',
                6,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#solutionAvailable'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'true'
            ),
            array(
                'sample2.xml',
                'choice',
                7,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#toolName'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'XMLSPY'
            ),
            array(
                'sample2.xml',
                'choice',
                8,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#toolVersion'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                '5.4'
            ),
            array(
                'sample2.xml',
                'choice',
                9,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#toolVendor'
                ),
                'choice',
                'imsqti_item_xmlv2p0',
                'choice.xml',
                'ALTOVA'
            ),
            array(
                'sample2.xml',
                'hybrid',
                0,
                array(
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                    'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
                ),
                'hybrid',
                'imsqti_item_xmlv2p1',
                'hybrid.xml',
                'qti_v2_item_02'
            ),
            array(
                'sample2.xml',
                'hybrid',
                1,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#interactionType'
                ),
                'hybrid',
                'imsqti_item_xmlv2p1',
                'hybrid.xml',
                'choiceInteraction'
            ),
            array(
                'sample2.xml',
                'hybrid',
                2,
                array(
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                    'http://www.imsglobal.org/xsd/imsqti_v2p0#interactionType'
                ),
                'hybrid',
                'imsqti_item_xmlv2p1',
                'hybrid.xml',
                'orderInteraction'
            ),
                        
            // Sample #3.
            array(
                'sample3.xml',
                'Q01',
                0,
                array(
                    'http://www.taotesting.com/xsd/mpm#myprojectMetadata',
                    'http://www.taotesting.com/xsd/mpm#complexity'
                ),
                'Q01',
                'imsqti_item_xmlv2p1',
                'Q01/qti.xml',
                '4'                
            ),
                        
            // Sample #4.
            array(
                'sample4.xml',
                'Q01',
                0,
                array(
                    'http://www.taotesting.com/xsd/ypm#myprojectMetadata',
                    'http://www.taotesting.com/xsd/ypm#complexity'
                ),
                'Q01',
                'imsqti_item_xmlv2p1',
                'Q01/qti.xml',
                '4'
            )
        );
    }
    
    /**
     * @dataProvider wrongTypeAsInputProvider
     * 
     * @param mixed $input
     */
    public function testWrongTypeAsInput($input)
    {
        $this->setExpectedException('oat\\taoQtiItem\\model\\qti\\metadata\\MetadataExtractionException');
        $values = $this->imsManifestExtractor->extract($input);
    }
    
    public function wrongTypeAsInputProvider()
    {
        return array(
            array(true),
            array('string'),
            array(array()),
            array(10),
            array(13.37),
            array(null),
            array(new stdClass())                
        );
    }
}