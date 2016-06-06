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
use oat\taoQtiItem\model\qti\metadata\guardians\LomIdentifierGuardian;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class LomIdentifierGuardianTest extends TaoPhpUnitTestRunner
{
    private static $itemResource;
    
    public static function setUpBeforeClass()
    {
        // Import LomIdentifier sample.
        $itemClass = \taoItems_models_classes_ItemsService::singleton()->getRootClass();
        
        // Register Metadata Injector.
        \oat\taoQtiItem\model\qti\Service::singleton()->getMetadataRegistry()->registerMetadataInjector('oat\taoQtiItem\model\qti\metadata\ontology\LomInjector');
        
        // Register Metadata Extractor.
        \oat\taoQtiItem\model\qti\Service::singleton()->getMetadataRegistry()->registerMetadataExtractor('oat\taoQtiItem\model\qti\metadata\imsManifest\ImsManifestMetadataExtractor');
        
        // Register Metadata Guardian.
        \oat\taoQtiItem\model\qti\Service::singleton()->getMetadataRegistry()->registerMetadataGuardian('oat\taoQtiItem\model\qti\metadata\guardians\LomIdentifierGuardian');
        
        $samplePath = dirname(__FILE__) . '/../samples/metadata/metadataGuardians/lomidentifieritem.zip';
        $report = \oat\taoQtiItem\model\qti\ImportService::singleton()->importQTIPACKFile($samplePath, $itemClass, true);
        $successes = $report->getSuccesses();
        self::$itemResource = $successes[0]->getData();
    }
    
    public function testLomIdentifierGuardian()
    {
        $itemClass = \taoItems_models_classes_ItemsService::singleton()->getRootClass();
        $samplePath = dirname(__FILE__) . '/../samples/metadata/metadataGuardians/lomidentifieritem.zip';
        $report = \oat\taoQtiItem\model\qti\ImportService::singleton()->importQTIPACKFile($samplePath, $itemClass, true);
        
        // Report must contain an information message.
        $this->assertTrue($report->contains(\common_report_Report::TYPE_INFO));
        $this->assertEquals(1, count($report->getInfos()));
    }
    
    public static function tearDownAfterClass()
    {
        \taoItems_models_classes_ItemsService::singleton()->deleteItem(self::$itemResource);
        
        // Unegister Metadata Injector.
        \oat\taoQtiItem\model\qti\Service::singleton()->getMetadataRegistry()->unregisterMetadataInjector('oat\taoQtiItem\model\qti\metadata\ontology\LomInjector');
        
        // Unregister Metadata Extractor.
        \oat\taoQtiItem\model\qti\Service::singleton()->getMetadataRegistry()->unregisterMetadataExtractor('oat\taoQtiItem\model\qti\metadata\imsManifest\ImsManifestMetadataExtractor');
        
        // Unregister Metadata Guardian.
        \oat\taoQtiItem\model\qti\Service::singleton()->getMetadataRegistry()->unregisterMetadataGuardian('oat\taoQtiItem\model\qti\metadata\guardians\LomIdentifierGuardian');
    }
}