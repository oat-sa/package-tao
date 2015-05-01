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

use oat\taoQtiItem\model\qti\metadata\simple\SimpleMetadataValue;
use oat\taoQtiItem\model\qti\metadata\imsManifest\ImsManifestMapping;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\qti\metadata\imsManifest\ImsManifestMetadataInjector;
use \DOMXPath;
use \DOMDocument;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class ImsManifestInjectionTest extends TaoPhpUnitTestRunner
{
    protected $imsManifestInjector;
    
    public function setUp()
    {
        parent::setUp();
        $this->imsManifestInjector = new ImsManifestMetadataInjector();
    }
    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->imsManifestInjector);
    }
    
    /**
     * @dataProvider injectionProvider
     * 
     * @param string $inputFile
     * @param array $values
     * @param array $mappings
     */
    public function testInjection($inputFile, array $values, array $mappings)
    {
        $imsManifest = new DOMDocument('1.0', 'UTF-8');
        $imsManifest->load(dirname(__FILE__) . "/../samples/metadata/imsManifestInjection/${inputFile}");
        
        
        // Register mappings...
        foreach ($mappings as $mapping) {
            $this->imsManifestInjector->addMapping($mapping);
        }
        
        $this->imsManifestInjector->inject($imsManifest, $values);
        $newDom = new DOMDocument('1.0', 'UTF-8');
        $newDom->loadXML($imsManifest->saveXML());
        $imsManifest = $newDom;
        
        $xpath = new DOMXpath($imsManifest);
        $xpath->registerNamespace('man', $imsManifest->documentElement->namespaceURI);
        
        // Check everything is fine regarding mappings...
        foreach ($mappings as $mapping) {
            $prefix = $mapping->getPrefix();
            $ns = $mapping->getNamespace();
            $sc = $mapping->getSchemaLocation();
            
            $xpath->registerNamespace($prefix, $ns);
            $manifestElt = $imsManifest->documentElement;
            $this->assertEquals('manifest', $manifestElt->tagName, "No <manifest> element found as the root XML element for file '${inputFile}'.");
            
            // Check that the namespace is correctly declared in <manifest> element.
            $this->assertTrue($manifestElt->hasAttributeNS('http://www.w3.org/2000/xmlns/', "${prefix}"), "No namespace with prefix '${prefix}' declared in <manifest> element for file '${inputFile}'.");
            $nsDeclaration = $manifestElt->getAttribute("xmlns:${prefix}");
            $this->assertEquals($ns, $nsDeclaration, "Namespace declaration for namespace '${ns}' with prefix '${prefix}' in <manifest> element does not match for file '${inputFile}'.");
            
            // Check that we get the tuple in xsi:schemaLocation.
            $this->assertTrue($manifestElt->hasAttribute('xsi:schemaLocation'), "No xsd:schemaLocation attribute found in <manifest> element for file '${inputFile}'.");
            $schemaLocations = $manifestElt->getAttribute('xsi:schemaLocation');
            
            $xsiPattern = '@' . preg_quote($ns) . "\\s+" . preg_quote($sc) . '@';
            $this->assertEquals(1, preg_match($xsiPattern, $schemaLocations), "No xsi:schemaLocation found for namespace '${ns}' in file '${inputFile}'.");
        }
        
        foreach ($values as $resourceIdentifier => $metadataValues) {

            foreach ($metadataValues as $metadataValue) {
                $path = $metadataValue->getPath();
                $query = "//man:resource[@identifier='${resourceIdentifier}']/man:metadata";
                
                foreach ($path as $pathComponent) {
                    $parts = explode('#', $pathComponent);
                    $base = $parts[0];
                    $tag = $parts[1];
                    
                    // do we have a namespace mapping for that?
                    $mappings = $this->imsManifestInjector->getMappings();
                    $mapping = null;
                    foreach ($mappings as $m) {
                        if ($m->getNamespace() === $base) {
                            $mapping = $m;
                            break;
                        }
                    }
                    
                    $prefix = $mapping->getPrefix();
                    $query .= "/${prefix}:${tag}";
                }
                
                // Do we have something at location?
                $elts = $xpath->query($query);
                $this->assertGreaterThanOrEqual(1, $elts->length, "Nothing found in XML at path '" . implode(' -> ', $path) . "' in file '${inputFile}'.");
                $hasLang = $metadataValue->getLanguage() !== '';
                
                // Does one of the values contain the expected value?
                for ($i = 0; $i < $elts->length; $i++) {
                    $valueMatch = $elts->item($i)->nodeValue === $metadataValue->getValue();
                    $langMatch = false;
                    
                    if ($hasLang === false || $elts->item($i)->getAttribute('xml:lang') === $metadataValue->getLanguage()) {
                        $langMatch = true;
                    }
                    
                    if ($valueMatch === true && $langMatch === true) {
                        break;
                    }
                }
                
                $this->assertLessThan($elts->length, $i, "No matching value found at path '" . implode(' -> ', $path) . "' in file '${inputFile}'.");
            }
        }
    }
    
    public function injectionProvider()
    {
        return array(
            array(
                'sample1.xml',
                array(
                    'choice' => array(
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
                            ),
                            'qti_v2_item_01'
                        )
                    )
                ),
                array(
                    new ImsManifestMapping('http://www.imsglobal.org/xsd/imsmd_v1p2', 'imsmd', 'http://www.imsglobal.org/xsd/imsmd_v1p2p2.xsd')                
                )
            ),
            
            array(
                'sample2.xml',
                array(
                    'choice' => array(
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
                            ),
                            'qti_v2_item_01'
                        ),
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#title',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#langstring'
                            ),
                            'Metadata Example Item #1',
                            'en'
                        ),
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#description',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#langstring'
                            ),
                            'This is a dummy item',
                            'en'
                        ),
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#timeDependent'
                            ),
                            'false'
                        ),
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#interactionType'
                            ),
                            'choiceInteraction'
                        ),
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#feedbackType'
                            ),
                            'nonadaptive'
                        ),
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#solutionAvailable'
                            ),
                            'true'
                        ),
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#toolName'
                            ),
                            'XMLSPY'
                        ),
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#toolVersion'
                            ),
                            '5.4'
                        ),
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#toolVendor'
                            ),
                            'ALTOVA'
                        )
                    ),
                    'hybrid' => array(
                        new SimpleMetadataValue(
                            'hybrid',
                            array(
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#lom',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#general',
                                'http://www.imsglobal.org/xsd/imsmd_v1p2#identifier'
                            ),
                            'qti_v2_item_02'
                        ),
                        new SimpleMetadataValue(
                            'hybrid',
                            array(
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#interactionType'
                            ),
                            'choiceInteraction'
                        ),
                        new SimpleMetadataValue(
                            'hybrid',
                            array(
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#qtiMetadata',
                                'http://www.imsglobal.org/xsd/imsqti_v2p0#interactionType'
                            ),
                            'orderInteraction'
                        ),
                    ),
                ),
                array(
                    new ImsManifestMapping('http://www.imsglobal.org/xsd/imsmd_v1p2', 'imsmd', 'http://www.imsglobal.org/xsd/imsmd_v1p2p2.xsd'),
                    new ImsManifestMapping('http://www.imsglobal.org/xsd/imsqti_v2p0', 'imsqti', 'http://www.imsglobal.org/xsd/imsqti_v2p0.xsd')
                )
            ),
                        
            array(
                'sample3.xml',
                array(
                    'Q01' => array(
                        new SimpleMetadataValue(
                            'choice',
                            array(
                                'http://www.taotesting.com/xsd/mpm#myprojectMetadata',
                                'http://www.taotesting.com/xsd/mpm#complexity'
                            ),
                            '4'
                        )
                    )
                ),
                array(
                    new ImsManifestMapping('http://www.taotesting.com/xsd/mpm', 'mpm', 'http://www.taotesting.com/xsd/mpm.xsd')
                )
            ),
        );
    }
}