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

use qtism\data\storage\xml\XmlDocument;

/**
 * A specialization of QTI ItemExporter aiming at exporting IMS QTI Test definitions from the TAO
 * platform to a ZIP archive.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiTest_models_classes_export_QtiTestExporter extends taoItems_models_classes_ItemExporter
{

    /**
     * The QTISM XmlDocument representing the Test to be
     * exported.
     *
     * @var XmlDocument
     */
    private $testDocument;

    /**
     * A reference to the QTI Test Service.
     *
     * @var taoQtiTest_models_classes_QtiTestService
     */
    private $testService;

    /**
     * An array of items related to the current Test Export. The array is associative. Its
     * keys are actually the assessmentItemRef identifiers.
     *
     * @var array
     */
    private $items;

    /**
     * A DOMDocument representing the IMS Manifest to be
     * populated while exporting the Test.
     *
     * @var DOMDocument
     */
    private $manifest = null;

    /**
     * Create a new instance of QtiTestExport.
     *
     * @param core_kernel_classes_Resource $test The Resource in the ontology representing the QTI Test to be exported.
     * @param ZipArchive $zip An instance of ZipArchive were components of the QTI Test will be stored into.
     * @param DOMDocument $manifest A DOMDocument representing the IMS Manifest to be populated during the Test Export.
     */
    public function __construct(core_kernel_classes_Resource $test, ZipArchive $zip, DOMDocument $manifest)
    {
        parent::__construct($test, $zip);
        $this->setTestService(taoQtiTest_models_classes_QtiTestService::singleton());
        $this->setTestDocument($this->getTestService()->getDoc($test));
        $this->setItems($this->getTestService()->getItems($test));
        $this->setManifest($manifest);
    }

    /**
     * Set the QTISM XmlDocument which holds the QTI Test definition to be exported.
     *
     * @param XmlDocument $testDocument
     */
    protected function setTestDocument(XmlDocument $testDocument)
    {
        $this->testDocument = $testDocument;
    }

    /**
     * Get the QTISM XmlDocument which holds the QTI Test definition to be exported.
     *
     * @return XmlDocument
     */
    protected function getTestDocument()
    {
        return $this->testDocument;
    }

    /**
     * Set a reference on the QTI Test Service.
     *
     * @param taoQtiTest_models_classes_QtiTestService $service
     */
    protected function setTestService(taoQtiTest_models_classes_QtiTestService $service)
    {
        $this->testService = $service;
    }

    /**
     * Get a reference on the QTI Test Service.
     *
     * @return taoQtiTest_models_classes_QtiTestService
     */
    protected function getTestService()
    {
        return $this->testService;
    }

    /**
     * Set the array of items that are involved in the QTI Test Definition to
     * be exported.
     *
     * @param array $items An associative array where keys are assessmentItemRef identifiers and values are core_kernel_classes_Resource objects representing the items in the knowledge base.
     */
    protected function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * Get the array of items that are involved in the QTI Test Definition
     * to be exported.
     *
     * @return array An associative array where keys are assessmentItemRef identifiers and values are core_kernel_classes_Resource objects representing the items in the knowledge base.
     */
    protected function getItems()
    {
        return $this->items;
    }

    /**
     * Set the DOMDocument representing the IMS Manifest to be
     * populated during Test Export.
     *
     * @param DOMDocument $manifest
     */
    protected function setManifest(DOMDocument $manifest)
    {
        $this->manifest = $manifest;
    }

    /**
     * Get the DOMDocument representing the IMS Manifest to
     * be populated during Test Export.
     *
     * @return DOMDocument
     */
    public function getManifest()
    {
        return $this->manifest;
    }

    /**
     * Export the test definition and all its dependencies (media, items, ...) into
     * the related ZIP archive.
     *
     * @param array $options An array of options (not used by this implementation).
     * @return common_report_Report
     */
    public function export($options = array())
    {
        // 1. Export the items bound to the test.
        $report = $this->exportItems();
        $itemIdentifiers = $report->getData();

        // 2. Export the test definition itself.
        $this->exportTest($itemIdentifiers);

        // 3. Persist manifest in archive.
        $this->getZip()->addFromString('imsmanifest.xml', $this->getManifest()->saveXML());

        return $report;
    }

    /**
     * Export the dependent items into the ZIP archive.
     *
     * @return common_report_Report that contains An array of identifiers that were assigned to exported items into the IMS Manifest.
     */
    protected function exportItems()
    {
        $report = common_report_Report::createSuccess();
        $subReport = common_report_Report::createSuccess();
        $identifiers = array();
        $testPath = $this->getTestService()->getTestContent($this->getItem())->getAbsolutePath();
        $extraPath = trim(str_replace(array($testPath, TAOQTITEST_FILENAME), '',
            $this->getTestService()->getDocPath($this->getItem())), DIRECTORY_SEPARATOR);
        $extraPath = str_replace(DIRECTORY_SEPARATOR, '/', $extraPath);

        $extraReversePath = '';
        if (empty($extraPath) === false) {
            $n = count(explode('/', $extraPath));
            $parts = array();

            for ($i = 0; $i < $n; $i++) {
                $parts[] = '..';
            }

            $extraReversePath = implode('/', $parts) . DIRECTORY_SEPARATOR;
        }

        foreach ($this->getItems() as $refIdentifier => $item) {
            $itemExporter = new taoQtiTest_models_classes_export_QtiItemExporter($item, $this->getZip(), $this->getManifest());
            if (!in_array($itemExporter->buildIdentifier(), $identifiers)) {
                $identifiers[] = $itemExporter->buildIdentifier();
                $subReport = $itemExporter->export();
            }

            // Modify the reference to the item in the test definition.
            $newQtiItemXmlPath = $extraReversePath . '../../items/' . tao_helpers_Uri::getUniqueId($item->getUri()) . '/qti.xml';
            $itemRef = $this->getTestDocument()->getDocumentComponent()->getComponentByIdentifier($refIdentifier);
            $itemRef->setHref($newQtiItemXmlPath);

            if ($report->getType() !== common_report_Report::TYPE_ERROR &&
                ($subReport->containsError() || $subReport->getType() === common_report_Report::TYPE_ERROR)
            ) {
                $report->setType(common_report_Report::TYPE_ERROR);
                $report->setMessage(__('Export error in test : %s', $this->getItem()->getLabel()));
            }
            $report->add($subReport);
        }
        $report->setData($identifiers);

        return $report;
    }

    /**
     * Export the Test definition itself and its related media.
     *
     * @param array $itemIdentifiers An array of identifiers that were assigned to exported items into the IMS Manifest.
     */
    protected function exportTest(array $itemIdentifiers)
    {
        // Serialize the test definition somewhere and add
        // it to the archive.
        $tmpPath = tempnam('/tmp', 'tao');
        $this->getTestDocument()->save($tmpPath);
        $testPath = $this->getTestService()->getTestContent($this->getItem())->getAbsolutePath();

        // Add the test definition in the archive.
        $testBasePath = 'tests/' . tao_helpers_Uri::getUniqueId($this->getItem()->getUri()) . '/';
        $extraPath = trim(str_replace(array($testPath, TAOQTITEST_FILENAME), '',
            $this->getTestService()->getDocPath($this->getItem())), DIRECTORY_SEPARATOR);

        $testHref = $testBasePath . ((empty($extraPath) === false) ? $extraPath . '/' : '') . 'test.xml';

        common_Logger::t('TEST DEFINITION AT: ' . $testHref);
        $this->addFile($tmpPath, $testHref);
        $this->referenceTest($testHref, $itemIdentifiers);


        $files = tao_helpers_File::scandir($testPath, array('recursive' => true, 'absolute' => true));
        foreach ($files as $f) {
            // Only add dependency files...
            if (is_dir($f) === false && strpos($f, TAOQTITEST_FILENAME) === false) {
                // Add the file to the archive.
                $fileHref = $testBasePath . ltrim(str_replace($testPath, '', $f), '/');
                common_Logger::t('AUXILIARY FILE AT: ' . $fileHref);
                $this->getZip()->addFile($f, $fileHref);
                $this->referenceAuxiliaryFile($fileHref);
            }
        }
    }

    /**
     * Reference the test into the IMS Manifest.
     *
     * @param string $href The path (base path is the ZIP archive) to the test definition.
     * @param array $itemIdentifiers An array of identifiers that were assigned to exported items into the IMS Manifest.
     */
    protected function referenceTest($href, array $itemIdentifiers)
    {
        $identifier = tao_helpers_Uri::getUniqueId($this->getItem()->getUri());
        $manifest = $this->getManifest();

        // Identifiy the target node.
        $resources = $manifest->getElementsByTagName('resources');
        $targetElt = $resources->item(0);

        // Create the IMS Manifest <resource> element.
        $resourceElt = $manifest->createElement('resource');
        $resourceElt->setAttribute('identifier', $identifier);
        $resourceElt->setAttribute('type', 'imsqti_test_xmlv2p1');
        $resourceElt->setAttribute('href', $href);
        $targetElt->appendChild($resourceElt);

        // Append an IMS Manifest <file> element referencing the test definition.
        $fileElt = $manifest->createElement('file');
        $fileElt->setAttribute('href', $href);
        $resourceElt->appendChild($fileElt);

        foreach ($itemIdentifiers as $itemIdentifier) {
            $this->referenceDependency($itemIdentifier);
        }
    }

    /**
     * Reference a test dependency (i.e. Items related to the test) in the IMS Manifest.
     *
     * @param string $identifierRef The identifier of the item resource in the IMS Manifest.
     */
    protected function referenceDependency($identifierRef)
    {
        $xpath = new DOMXpath($this->getManifest());
        $identifier = tao_helpers_Uri::getUniqueId($this->getItem()->getUri());
        $manifest = $this->getManifest();

        $search = $xpath->query("//resource[@identifier='${identifier}']");
        $resourceElt = $search->item(0);

        // Append IMS Manifest <dependency> elements referencing $identifierRef.
        $dependencyElt = $manifest->createElement('dependency');
        $dependencyElt->setAttribute('identifierref', $identifierRef);
        $resourceElt->appendChild($dependencyElt);
    }

    /**
     * Reference a Test Auxiliary File (e.g. media, stylesheet, ...) in the IMS Manifest.
     *
     * @param string $href The path (base path is the ZIP archive) to the auxiliary file in the ZIP archive.
     */
    protected function referenceAuxiliaryFile($href)
    {
        $manifest = $this->getManifest();
        $testIdentifier = tao_helpers_Uri::getUniqueId($this->getItem()->getUri());
        $xpath = new DOMXPath($manifest);

        // Find the first <dependency> element.
        $dependencies = $xpath->query("//resource[@identifier='${testIdentifier}']/dependency");
        $firstDependencyElt = $dependencies->item(0);

        // Create an IMS Manifest <file> element.
        $fileElt = $manifest->createElement('file');
        $fileElt->setAttribute('href', ltrim($href, '/'));

        $firstDependencyElt->parentNode->insertBefore($fileElt, $firstDependencyElt);
    }
}
