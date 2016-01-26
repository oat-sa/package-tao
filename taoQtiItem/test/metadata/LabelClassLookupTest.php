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
use oat\taoQtiItem\model\qti\metadata\classLookups\LabelClassLookup;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class LabelClassLookupTest extends TaoPhpUnitTestRunner
{
    private static $itemResource;
    
    public static function setUpBeforeClass()
    {
        $itemClass = \taoItems_models_classes_ItemsService::singleton()->getRootClass();
        
        // Register Metadata ClassLookup.
        \oat\taoQtiItem\model\qti\Service::singleton()->getMetadataRegistry()->registerMetadataClassLookup('oat\taoQtiItem\model\qti\metadata\classLookups\LabelClassLookup');
        
        // Register Metadata Extractor.
        \oat\taoQtiItem\model\qti\Service::singleton()->getMetadataRegistry()->registerMetadataExtractor('oat\taoQtiItem\model\qti\metadata\imsManifest\ImsManifestMetadataExtractor');
        
        // Create fake class.
        \core_kernel_classes_ClassFactory::createSubClass($itemClass, 'mytestclasslabel', 'mytestclasslabel', 'http://www.test.com#mytestclass');
        
        // Import myTestClassLabel sample...
        $samplePath = dirname(__FILE__) . '/../samples/metadata/metadataClassLookups/mytestclasslabel.zip';
        $report = \oat\taoQtiItem\model\qti\ImportService::singleton()->importQTIPACKFile($samplePath, $itemClass, true);
        $successes = $report->getSuccesses();
        self::$itemResource = $successes[0]->getData();
    }
    
    public function testLabelClassLookupTest()
    {
        $class = new \core_kernel_classes_Class('http://www.test.com#mytestclass');
        $this->assertEquals(1, count($class->countInstances()));
    }
    
    public static function tearDownAfterClass()
    {
        \taoItems_models_classes_ItemsService::singleton()->deleteItem(self::$itemResource);
        
        // Unregister Metadata ClassLookup.
        \oat\taoQtiItem\model\qti\Service::singleton()->getMetadataRegistry()->unregisterMetadataClassLookup('oat\taoQtiItem\model\qti\metadata\classLookups\LabelClassLookup');
        
        // Unregister Metadata Extractor.
        \oat\taoQtiItem\model\qti\Service::singleton()->getMetadataRegistry()->unregisterMetadataExtractor('oat\taoQtiItem\model\qti\metadata\imsManifest\ImsManifestMetadataExtractor');
        
        // Delete fake class
        $class = new \core_kernel_classes_Class('http://www.test.com#mytestclass');
        $class->delete(true);
    }
}