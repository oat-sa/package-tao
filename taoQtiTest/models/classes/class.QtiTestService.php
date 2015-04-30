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

use oat\taoQtiItem\model\qti\Resource;
use oat\taoQtiItem\model\qti\ImportService;
use qtism\data\storage\StorageException;
use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\xml\marshalling\UnmarshallingException;
use qtism\data\QtiComponentCollection;
use qtism\data\SectionPartCollection;
use qtism\data\AssessmentItemRef;

/**
 * the QTI TestModel service.
 *
 * @author Joel Bout <joel@taotesting.com>
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package taoQtiTest

 */
class taoQtiTest_models_classes_QtiTestService extends taoTests_models_classes_TestsService {

    const CONFIG_QTITEST_FOLDER = 'qtiTestFolder';

    const CONFIG_QTITEST_ACCEPTABLE_LATENCY = 'qtiAcceptableLatency';

    /**
     * Get the QTI Test document formated in JSON.
     *
     * @param core_kernel_classes_Resource $test
     * @return string the json
     * @throws taoQtiTest_models_classes_QtiTestServiceException
     */
    public function getJsonTest(core_kernel_classes_Resource $test)
    {
        $doc = $this->getDoc($test);
        $converter = new taoQtiTest_models_classes_QtiTestConverter($doc);
        return $converter->toJson();
    }

    /**
     *
     * @see taoTests_models_classes_TestsService::setDefaultModel()
     */
    protected function setDefaultModel($test)
    {
        $this->setTestModel($test, new core_kernel_classes_Resource(INSTANCE_TEST_MODEL_QTI));
    }

    /**
     * Save the json formated test into the test resource.
     *
     * @param core_kernel_classes_Resource $test
     * @param string $json
     * @return boolean true if saved
     * @throws taoQtiTest_models_classes_QtiTestServiceException
     */
    public function saveJsonTest(core_kernel_classes_Resource $test, $json) {
        $saved = false;

        if (! empty($json)) {
            $doc = $this->getDoc($test);

            $converter = new taoQtiTest_models_classes_QtiTestConverter($doc);
            $converter->fromJson($json);

            $saved = $this->saveDoc($test, $doc);
        }
        return $saved;
    }

    public function fromJson($json)
    {
        $doc = new XmlDocument('2.1');
        $converter = new taoQtiTest_models_classes_QtiTestConverter($doc);
        $converter->fromJson($json);
        return $doc;
    }

    /**
     * Get the items that are part of a given $test.
     *
     * @param core_kernel_classes_Resource $test A Resource describing a QTI Assessment Test.
     * @return array An array of core_kernel_classes_Resource objects. The array is associative. Its keys are actually the assessmentItemRef identifiers.
     */
    public function getItems(core_kernel_classes_Resource $test)
    {
        return $this->getDocItems($this->getDoc($test));
    }

    /**
     * Assign items to a test and save it.
     * @param core_kernel_classes_Resource $test
     * @param array $items
     * @return boolean true if set
     * @throws taoQtiTest_models_classes_QtiTestServiceException
     */
    public function setItems(core_kernel_classes_Resource $test, array $items)
    {
        $doc = $this->getDoc($test);
        $bound = $this->setItemsToDoc($doc, $items);

        if($this->saveDoc($test, $doc)){
            return $bound == count($items);
        }

        return false;
    }

      /**
     * Save the QTI test : set the items sequence and some options.
     *
     * @param core_kernel_classes_Resource $test A Resource describing a QTI Assessment Test.
     * @param array $items the items sequence
     * @param array $options the test's options
     * @return boolean if nothing goes wrong
     * @throws StorageException If an error occurs while serializing/unserializing QTI-XML content.
     */
    public function save( core_kernel_classes_Resource $test, array $items) {
        try {
            $doc = $this->getDoc($test);
            $this->setItemsToDoc($doc, $items);
            $saved  = $this->saveDoc($test, $doc);
        }
    	catch (StorageException $e) {
    		throw new taoQtiTest_models_classes_QtiTestServiceException(
                        "An error occured while dealing with the QTI-XML test: ".$e->getMessage(),
                        taoQtiTest_models_classes_QtiTestServiceException::TEST_WRITE_ERROR
                   );
    	}

    	return $saved;
    }

    /**
     * Get an identifier for a component of $qtiType.
     * This identifier must be unique across the whole document.
     *
     * @param XmlDocument $doc
     * @param type $qtiType the type name
     * @return the identifier
     */
    public function getIdentifierFor(XmlDocument $doc, $qtiType)
    {
        $components = $doc->getDocumentComponent()->getIdentifiableComponents();
        $index = 1;
        do {
            $identifier = $this->generateIdentifier($doc, $qtiType, $index);
            $index ++;
        } while (! $this->isIdentifierUnique($components, $identifier));

        return $identifier;
    }

    /**
     * Check whether an identifier is unique against a list of components
     *
     * @param QtiComponentCollection $components
     * @param type $identifier
     * @return boolean
     */
    private function isIdentifierUnique(QtiComponentCollection $components, $identifier)
    {
        foreach ($components as $component) {
            if ($component->getIdentifier() == $identifier) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generate an identifier from a qti type, using the syntax "qtitype-index"
     *
     * @param XmlDocument $doc
     * @param type $qtiType
     * @param type $offset
     * @return the identifier
     */
    private function generateIdentifier(XmlDocument $doc, $qtiType, $offset = 1)
    {
        $typeList = $doc->getDocumentComponent()->getComponentsByClassName($qtiType);
        return $qtiType . '-' . (count($typeList) + $offset);
    }

    /**
     * Import a QTI Test Package containing one or more QTI Test definitions.
     *
     * @param core_kernel_classes_Class $targetClass The Target RDFS class where you want the Test Resources to be created.
     * @param string $file The path to the IMS archive you want to import tests from.
     * @return common_report_Report An import report.
     */
    public function importMultipleTests(core_kernel_classes_Class $targetClass, $file) {

        $testClass = $targetClass;
        $report = new common_report_Report(common_report_Report::TYPE_INFO);
        $validPackage = false;
        $validManifest = false;

        // Validate the given IMS Package itself (ZIP integrity, presence of an 'imsmanifest.xml' file.
        $invalidArchiveMsg = __("The provided archive is invalid. Make sure it is not corrupted and that it contains an 'imsmanifest.xml' file.");

        try {
            $qtiPackageParser = new taoQtiTest_models_classes_PackageParser($file);
            $qtiPackageParser->validate();
            $validPackage = true;
        }
        catch (Exception $e) {
            $report->add(common_report_Report::createFailure($invalidArchiveMsg));
        }

        // Validate the manifest (well formed XML, valid against the schema).
        if ($validPackage === true) {
            $folder = $qtiPackageParser->extract();

            if (is_dir($folder) === false) {
                $report->add(common_report_Report::createFailure($invalidArchiveMsg));
            } else {

                $qtiManifestParser = new taoQtiTest_models_classes_ManifestParser($folder . 'imsmanifest.xml');
                $qtiManifestParser->validate();

                if ($qtiManifestParser->isValid() === true) {

                    $validManifest = true;

                    $tests = $qtiManifestParser->getResources('imsqti_test_xmlv2p1');
                    foreach ($tests as $qtiTestResource) {
                        $report->add($this->importTest($testClass, $qtiTestResource, $qtiManifestParser, $folder));
                    }
                }
                else {
                    $msg = __("The 'imsmanifest.xml' file found in the archive is not valid.");
                    $report->add(common_report_Report::createFailure($msg));
                }

                // Cleanup the folder where the archive was extracted.
                tao_helpers_File::deltree($folder);

            }
        }

        if ($report->containsError() === true) {
            $report->setMessage(__('The IMS QTI Test Package could not be imported.'));
            $report->setType(common_report_Report::TYPE_ERROR);
        }
        else {
            $report->setMessage(__('IMS QTI Test Package successfully imported.'));
            $report->setType(common_report_Report::TYPE_SUCCESS);
        }

        if ($report->containsError() === true && $validPackage === true && $validManifest === true) {
            // We consider a test package as an atomic component, we then rollback it.
            $itemService = taoItems_models_classes_ItemsService::singleton();

            foreach ($report as $r) {
                $data = $r->getData();

                // Delete all imported items.
                foreach ($data->items as $item) {
                    common_Logger::i("Rollbacking item '" . $item->getLabel() . "'...");
                    @$itemService->deleteItem($item);
                }

                // Delete the target Item RDFS class.
                common_Logger::i("Rollbacking Items target RDFS class '" . $data->itemClass->getLabel() . "'...");
                @$data->itemClass->delete();

                // Delete test definition.
                common_Logger::i("Rollbacking test '" . $data->rdfsResource->getLabel() . "...");
                @$this->deleteTest($data->rdfsResource);

                if (count($data->items) > 0) {
                    $msg = __("The resources related to the IMS QTI Test referenced as \"%s\" in the IMS Manifest file were rolled back.", $data->manifestResource->getIdentifier());
                    $report->add(new common_report_Report(common_report_Report::TYPE_WARNING, $msg));
                }
            }
        }

        return $report;
    }

    /**
     * Import a QTI Test and its dependent Items into the TAO Platform.
     *
     * @param core_kernel_classes_Class $targetClass The RDFS Class where Ontology resources must be created.
     * @param oat\taoQtiItem\model\qti\Resource $qtiTestResource The QTI Test Resource representing the IMS QTI Test to be imported.
     * @param taoQtiTest_models_classes_ManifestParser $manifestParser The parser used to retrieve the IMS Manifest.
     * @param string $folder The absolute path to the folder where the IMS archive containing the test content
     * @return common_report_Report A report about how the importation behaved.
     */
    protected function importTest(core_kernel_classes_Class $targetClass, Resource $qtiTestResource, taoQtiTest_models_classes_ManifestParser $manifestParser, $folder) {

        $itemImportService = ImportService::singleton();
        $itemService = taoItems_models_classes_ItemsService::singleton();
        $testClass = $targetClass;

        // Create an RDFS resource in the knowledge base that will hold
        // the information about the imported QTI Test.
        $testResource = $this->createInstance($testClass);
        $qtiTestModelResource = new core_kernel_classes_Resource(INSTANCE_TEST_MODEL_QTI);
        $modelProperty = new core_kernel_classes_Property(PROPERTY_TEST_TESTMODEL);
        $testResource->editPropertyValues($modelProperty, $qtiTestModelResource);

        // Create the report that will hold information about the import
        // of $qtiTestResource in TAO.
        $report = new common_report_Report(common_report_Report::TYPE_INFO);

        // The class where the items that belong to the test will be imported.
        $itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
        $targetClass = $itemClass->createSubClass($testResource->getLabel());

        // Load and validate the manifest
        $qtiManifestParser = new taoQtiTest_models_classes_ManifestParser($folder . 'imsmanifest.xml');
        $qtiManifestParser->validate();

        // Set up $report with useful information for client code (especially for rollback).
        $reportCtx = new stdClass();
        $reportCtx->manifestResource = $qtiTestResource;
        $reportCtx->rdfsResource = $testResource;
        $reportCtx->itemClass = $targetClass;
        $reportCtx->items = array();
        $report->setData($reportCtx);

        // Expected test.xml file location.
        $expectedTestFile = $folder . str_replace('/', DIRECTORY_SEPARATOR, $qtiTestResource->getFile());

        // Already imported test items (qti xml file paths).
        $alreadyImportedTestItemFiles = array();

        // -- Check if the file referenced by the test QTI resource exists.
        if (is_readable($expectedTestFile) === false) {
            $report->add(common_report_Report::createFailure(__('No file found at location "%s".', $qtiTestResource->getFile())));
        }
        else {
            // -- Load the test in a QTISM flavour.
            $testDefinition = new XmlDocument();

            try {
                $testDefinition->load($expectedTestFile, true);

                // -- Load all items related to test.
                $itemError = false;

                // discover test's base path.
                $dependencies = taoQtiTest_helpers_Utils::buildAssessmentItemRefsTestMap($testDefinition, $manifestParser, $folder);

                if (count($dependencies) > 0) {

                    foreach ($dependencies as $assessmentItemRefId => $qtiDependency) {

                        if ($qtiDependency !== false) {

                            if (Resource::isAssessmentItem($qtiDependency->getType())) {

                                $qtiFile = $folder . str_replace('/', DIRECTORY_SEPARATOR, $qtiDependency->getFile());

                                // Skip if $qtiFile already imported (multiple assessmentItemRef "hrefing" the same file).
                                if (array_key_exists($qtiFile, $alreadyImportedTestItemFiles) === false) {

                                    $itemReport = $itemImportService->importQTIFile($qtiFile, $targetClass);
                                    $rdfItem = $itemReport->getData();

                                    if ($rdfItem) {
                                        $itemPath = taoItems_models_classes_ItemsService::singleton()->getItemFolder($rdfItem);

                                        foreach ($qtiDependency->getAuxiliaryFiles() as $auxResource) {
                                            // $auxResource is a relativ URL, so we need to replace the slashes with directory separators
                                            $auxPath = $folder . str_replace('/', DIRECTORY_SEPARATOR, $auxResource);

                                            // does the file referenced by $auxPath exist?
                                            if (is_readable($auxPath) === true) {
                                                $relPath = helpers_File::getRelPath($qtiFile, $auxPath);
                                                $destPath = $itemPath . $relPath;
                                                tao_helpers_File::copy($auxPath, $destPath, true);
                                            }
                                            else {
                                                $msg = __('Auxiliary file not found at location "%s".', $auxResource);
                                                $itemReport->add(new common_report_Report(common_report_Report::TYPE_WARNING,$msg));
                                            }
                                        }

                                        $reportCtx->items[$assessmentItemRefId] = $rdfItem;
                                        $alreadyImportedTestItemFiles[$qtiFile] = $rdfItem;
                                        $itemReport->setMessage(__('IMS QTI Item referenced as "%s" in the IMS Manifest file successfully imported.', $qtiDependency->getIdentifier()));
                                    }
                                    else {
                                        $itemReport->setType(common_report_Report::TYPE_ERROR);
                                        $itemReport->setMessage(__('IMS QTI Item referenced as "%s" in the IMS Manifest file could not be imported.', $qtiDependency->getIdentifier()));
                                        $itemError = ($itemError === false) ? true : $itemError;
                                    }

                                    $report->add($itemReport);
                                }
                                else {
                                    $reportCtx->items[$assessmentItemRefId] = $alreadyImportedTestItemFiles[$qtiFile];
                                }
                            }
                        }
                        else {
                            $msg = __('The dependency to the IMS QTI AssessmentItemRef "%s" in the IMS Manifest file could not be resolved.', $assessmentItemRefId);
                            $report->add(common_report_Report::createFailure($msg));
                            $itemError = ($itemError === false) ? true : $itemError;
                        }
                    }

                    // If items did not produce errors, we import the test definition.
                    if ($itemError === false) {
                        common_Logger::i('Importing test...');

                        // Second step is to take care of the test definition and the related media (auxiliary files).

                        // 1. Import test definition (i.e. the QTI-XML Test file).
                        $testContent = $this->importTestDefinition($testResource, $testDefinition, $qtiTestResource, $reportCtx->items, $folder, $report);

                        if ($testContent !== false) {
                            // 2. Import test auxilliary files (e.g. stylesheets, images, ...).
                            $this->importTestAuxiliaryFiles($testContent, $qtiTestResource, $folder, $report);

                            // 3. Give meaningful names to resources.
                            $testTitle = $testDefinition->getDocumentComponent()->getTitle();
                            $testResource->setLabel($testDefinition->getDocumentComponent()->getTitle());
                            $targetClass->setLabel($testDefinition->getDocumentComponent()->getTitle());
                        }
                    }
                    else {
                        $msg = __("One or more dependent IMS QTI Items could not be imported.");
                        $report->add(common_report_Report::createFailure($msg));
                    }
                }
                else {
                    // No depencies found (i.e. no item resources bound to the test).
                    $msg = __("No reference to any IMS QTI Item found.");
                    $report->add(common_report_Report::createFailure($msg));
                }
            }
            catch (StorageException $e) {
                // Source of the exception = $testDefinition->load()
                // What is the reason ?
                $finalErrorString = '';
                $eStrs = array();

                if (($libXmlErrors = $e->getErrors()) !== null) {
                    foreach ($libXmlErrors as $libXmlError) {
                        $eStrs[] = __('XML error at line %1$d column %2$d "%3$s".', $libXmlError->line, $libXmlError->column, trim($libXmlError->message));
                    }
                }

                $finalErrorString = implode("\n", $eStrs);
                if (empty($finalErrorString) === true) {
                    // Not XML malformation related. No info from LibXmlErrors extracted.
                    if (($previous = $e->getPrevious()) != null) {

                        // Useful information could be found here.
                        $finalErrorString = $previous->getMessage();

                        if ($previous instanceof UnmarshallingException) {
                            $domElement = $previous->getDOMElement();
                            $finalErrorString = __('Inconsistency at line %1d:', $domElement->getLineNo()) . ' ' . $previous->getMessage();
                        }
                    }
                    else {
                        $finalErrorString = __("Unknown error.");
                    }
                }

                $msg = __("Error found in the IMS QTI Test:\n%s", $finalErrorString);
                $report->add(common_report_Report::createFailure($msg));
            }
        }

        if ($report->containsError() === false) {
            $report->setType(common_report_Report::TYPE_SUCCESS);
            $msg = __("IMS QTI Test referenced as \"%s\" in the IMS Manifest file successfully imported.", $qtiTestResource->getIdentifier());
            $report->setMessage($msg);
        }
        else {
            $report->setType(common_report_Report::TYPE_ERROR);
            $msg = __("The IMS QTI Test referenced as \"%s\" in the IMS Manifest file could not be imported.", $qtiTestResource->getIdentifier());
            $report->setMessage($msg);
        }

        return $report;
    }

    /**
     * Import the Test itself  by importing its QTI-XML definition into the system, after
     * the QTI Items composing the test were also imported.
     *
     * The $itemMapping argument makes the implementation of this method able to know
     * what are the items that were imported. The $itemMapping is an associative array
     * where keys are the assessmentItemRef's identifiers and the values are the core_kernel_classes_Resources of
     * the items that are now stored in the system.
     *
     * When this method returns false, it means that an error occured at the level of the content of the imported test
     * itself e.g. an item referenced by the test is not present in the content package. In this case, $report might
     * contain useful information to return to the client.
     *
     * @param core_kernel_classes_Resource $testResource A Test Resource the new content must be bind to.
     * @param XmlDocument $testDefinition An XmlAssessmentTestDocument object.
     * @param oat\taoQtiItem\model\qti\Resource $qtiResource The manifest resource describing the test to be imported.
     * @param array $itemMapping An associative array that represents the mapping between assessmentItemRef elements and the imported items.
     * @param string $extractionFolder The absolute path to the temporary folder containing the content of the imported IMS QTI Package Archive.
     * @param common_report_Report $report A Report object to be filled during the import.
     * @return core_kernel_file_File The newly created test content.
     * @throws taoQtiTest_models_classes_QtiTestServiceException If an unexpected runtime error occurs.
     */
    protected function importTestDefinition(core_kernel_classes_Resource $testResource, XmlDocument $testDefinition, Resource $qtiResource, array $itemMapping, $extractionFolder, common_report_Report $report) {

        foreach ($itemMapping as $itemRefId => $itemResource) {
            $itemRef = $testDefinition->getDocumentComponent()->getComponentByIdentifier($itemRefId);
            $itemRef->setHref($itemResource->getUri());
        }

        // Bind the newly created test content to the Test Resource in database.
        $ds = DIRECTORY_SEPARATOR;
        $testContent = $this->createContent($testResource);
        $testPath = $testContent->getAbsolutePath();
        $finalPath = taoQtiTest_helpers_Utils::storeQtiResource($testContent, $qtiResource, $extractionFolder, false, TAOQTITEST_FILENAME);

        // Delete template test.xml file (created by self::createContent() method) from the root.
        // (Absolutely necessary when the test.xml file is not in the root folder of the archive)
        unlink($testPath . $ds . TAOQTITEST_FILENAME);

        try {
            $testDefinition->save($finalPath);
        }
        catch (StorageException $e) {
            throw new taoQtiTest_models_classes_QtiTestServiceException("An error occured while saving the QTI-XML test.", taoQtiTest_models_classes_QtiTestServiceException::TEST_WRITE_ERROR);
        }

        return $testContent;
    }

    /**
     * Imports the auxiliary files (file elements contained in the resource test element to be imported) into
     * the TAO Test Content directory.
     *
     * If some file cannot be copied, warnings will be committed.
     *
     * @param core_kernel_file_File $testContent The pointer to the TAO Test Content directory where auxilliary files will be stored.
     * @param oat\taoQtiItem\model\qti\Resource $qtiResource The manifest resource describing the test to be imported.
     * @param string $extractionFolder The absolute path to the temporary folder containing the content of the imported IMS QTI Package Archive.
     * @param common_report_Report A report about how the importation behaved.
     */
    protected function importTestAuxiliaryFiles(core_kernel_file_File $testContent,Resource $qtiResource, $extractionFolder, common_report_Report $report) {

        foreach ($qtiResource->getAuxiliaryFiles() as $aux) {
            try {
                taoQtiTest_helpers_Utils::storeQtiResource($testContent, $aux, $extractionFolder);
            }
            catch (common_Exception $e) {
                $report->add(new common_report_Report(common_report_Report::TYPE_WARNING, __('Auxiliary file not found at location "%s".', $aux)));
            }
        }
    }

    /**
     * Get the core_kernel_file_File object corresponding to the location
     * of the test content (a directory!) on the file system.
     *
     * @param core_kernel_classes_Resource $test
     * @return null|core_kernel_file_File
     * @throws taoQtiTest_models_classes_QtiTestServiceException
     */
    public function getTestFile(core_kernel_classes_Resource $test){

        if(is_null($test)){
            throw new taoQtiTest_models_classes_QtiTestServiceException(
                    'The selected test is null',
                    taoQtiTest_models_classes_QtiTestServiceException::TEST_READ_ERROR
               );
        }

        $testModel = $test->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TEST_TESTMODEL));
        if(is_null($testModel) || $testModel->getUri() != INSTANCE_TEST_MODEL_QTI) {
            throw new taoQtiTest_models_classes_QtiTestServiceException(
                    'The selected test is not a QTI test',
                    taoQtiTest_models_classes_QtiTestServiceException::TEST_READ_ERROR
               );
        }
        $file = $test->getOnePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
        if(!is_null($file)){
            return new core_kernel_file_File($file);
        }
        return null;
    }

    /**
     * Get the QTI reprensentation of a test content.
     *
     * @param core_kernel_classes_Resource $test the test to get the content from
     * @param type $validate enable validation
     * @return XmlDocument the QTI representation from the test content
     * @throws taoQtiTest_models_classes_QtiTestServiceException
     */
    public function getDoc(core_kernel_classes_Resource $test) {

        $doc = new XmlDocument('2.1');
        $dir = $this->getTestFile($test);
        if (is_null($dir)) {
            $dir = $this->createContent($test);
        } else {
            $dir = new core_kernel_file_File($dir);
        }

        try {
            $filePath = $this->getDocPath($test);
            $doc->load($filePath);
        } catch (Exception $e) {
            throw new taoQtiTest_models_classes_QtiTestServiceException(
                    "An error occured while loading QTI-XML test file for test '".$test->getUri()."' : ".$e->getMessage(),
                    taoQtiTest_models_classes_QtiTestServiceException::TEST_READ_ERROR
                );
        }

        return $doc;
    }

    /**
     * Get the path of the QTI XML test definition of a given $test resource.
     *
     * @param core_kernel_classes_Resource $test
     * @throws Exception If no QTI-XML or multiple QTI-XML test definition were found.
     * @return string The absolute path to the QTI XML Test definition related to $test.
     */
    public function getDocPath(core_kernel_classes_Resource $test)
    {
        $dir = $this->getTestFile($test);
        $testPath = $dir->getAbsolutePath();
        $files = tao_helpers_File::scandir($testPath, array(
            'recursive' => true,
            'absolute' => true,
            'only' => tao_helpers_File::$FILE
        ));
        $dirContent = array();

        foreach ($files as $f) {
            $pathinfo = pathinfo($f);
            if ($pathinfo['filename'] . '.' . $pathinfo['extension'] === TAOQTITEST_FILENAME) {
                $dirContent[] = $f;
            }
        }

        if (count($dirContent) === 0) {
            throw new Exception('No QTI-XML test file found.');
        }
        else if (count($dirContent) > 1) {
            throw new Exception('Multiple QTI-XML test file found.');
        }

        $filePath = current($dirContent);
        return $filePath;
    }

    /**
     * Convenience method that extracts entries of a $path array that correspond
     * to a given $fileName.
     *
     * @param array $paths An array of strings representing absolute paths within a given directory.
     * @return array $extractedPath The paths that meet the $fileName criterion.
     */
    private function filterTestContentDirectory(array $paths, $fileName) {
        $returnValue = array();

        foreach ($paths as $path) {
            $pathinfo = pathinfo($path);
            $pattern = $pathinfo['filename'];

            if (!empty($pathinfo['extension'])) {
                $pattern .= $pathinfo['extension'];
            }

            if ($fileName === $pattern) {
                $returnValue[] = $path;
            }
        }

        return $returnValue;
    }

    /**
     * Get the items from a QTI test document.
     *
     * @param \qtism\data\storage\xml\XmlDocument $doc The QTI XML document to be inspected to retrieve the items.
     * @return An array of core_kernel_classes_Resource object indexed by assessmentItemRef->identifier (string).
     */
    private function getDocItems(XmlDocument $doc){
        $itemArray = array();
    	foreach ($doc->getDocumentComponent()->getComponentsByClassName('assessmentItemRef') as $itemRef) {
            $itemArray[$itemRef->getIdentifier()] = new core_kernel_classes_Resource($itemRef->getHref());
    	}
    	return $itemArray;
    }

    /**
     * Assign items to a QTI test.
     * @param XmlDocument $doc
     * @param array $items
     * @return type
     * @throws taoQtiTest_models_classes_QtiTestServiceException
     */
    private function setItemsToDoc(XmlDocument $doc, array $items, $sectionIndex = 0) {

        $sections = $doc->getDocumentComponent()->getComponentsByClassName('assessmentSection');
        if(!isset($sections[$sectionIndex])){
            throw new taoQtiTest_models_classes_QtiTestServiceException(
                        'No section found in test at index : ' . $sectionIndex,
                        taoQtiTest_models_classes_QtiTestServiceException::TEST_READ_ERROR
                    );
        }
        $section = $sections[$sectionIndex];

        $itemContentProperty = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
        $itemRefs = new SectionPartCollection();
        $itemRefIdentifiers = array();
        foreach ($items as $itemResource) {
            $itemContent = new core_kernel_file_File($itemResource->getUniquePropertyValue($itemContentProperty));

            $itemDoc = new XmlDocument();

            try {
                $itemDoc->load($itemContent->getAbsolutePath());
            }
            catch (StorageException $e) {
                // We consider the item not compliant with QTI, let's try the next one.
                continue;
            }

            $itemRefIdentifier = $itemDoc->getDocumentComponent()->getIdentifier();

            //enable more than one reference
            if(array_key_exists($itemRefIdentifier, $itemRefIdentifiers)){
                    $itemRefIdentifiers[$itemRefIdentifier] += 1;
                    $itemRefIdentifier .= '-'. $itemRefIdentifiers[$itemRefIdentifier];
            } else {
                $itemRefIdentifiers[$itemRefIdentifier] = 0;
            }
            $itemRefs[] = new AssessmentItemRef($itemRefIdentifier, $itemResource->getUri());

        }
        $section->setSectionParts($itemRefs);



        return count($itemRefs);
    }

    /**
     * Save the content of test from a QTI Document
     * @param core_kernel_classes_Resource $test
     * @param qtism\data\storage\xml\XmlDocument $doc
     * @return boolean true if saved
     * @throws taoQtiTest_models_classes_QtiTestServiceException
     */
    private function saveDoc( core_kernel_classes_Resource $test, XmlDocument $doc){
        $saved = false;

        if(!is_null($test) && !is_null($doc)){
            $file = $this->getTestFile($test);
            if (!is_null($file)) {
                $testPath = $file->getAbsolutePath();
                try {
                    // Search for the test.xml file in the test content directory.
                    $files = tao_helpers_File::scandir($testPath, array('recursive' => true, 'absolute' => true, 'only' => tao_helpers_File::$FILE));
                    $dirContent = array();

                    foreach ($files as $f) {
                        $pathinfo = pathinfo($f);

                        if ($pathinfo['filename'] . '.' . $pathinfo['extension'] === TAOQTITEST_FILENAME) {
                            $dirContent[] = $f;
                        }
                    }

                    if (count($dirContent) === 0) {
                        throw new Exception('No QTI-XML test file found.');
                    }
                    else if (count($dirContent) > 1) {
                        throw new Exception('Multiple QTI-XML test file found.');
                    }

                    $finalPath = current($dirContent);
                    $doc->save($finalPath);
                    $saved = true;
                } catch (Exception $e) {
                    throw new taoQtiTest_models_classes_QtiTestServiceException(
                        "An error occured while writing QTI-XML test '${testPath}': ".$e->getMessage(),
                         taoQtiTest_models_classes_QtiTestServiceException::ITEM_WRITE_ERROR
                    );
                }
            }
        }
        return $saved;
    }

    /**
     * Create the defautl content directory of a QTI test.
     *
     * @param core_kernel_classes_Resource $test
     * @param boolean $createTestFile Whether or not create an empty QTI XML test file. Default is (boolean) true.
     * @return core_kernel_file_File the content file
     * @throws taoQtiTest_models_classes_QtiTestServiceException If a runtime error occurs while creating the test content.
     */
    public function createContent( core_kernel_classes_Resource $test, $createTestFile = true) {

    	$props = self::getQtiTestDirectory()->getPropertiesValues(array(
				PROPERTY_FILE_FILESYSTEM,
				PROPERTY_FILE_FILEPATH
			));

        $repository = new core_kernel_versioning_Repository(current($props[PROPERTY_FILE_FILESYSTEM]));
        $path = (string) current($props[PROPERTY_FILE_FILEPATH]);

        // $directory is the directory where test related resources will be stored.
        $directory = $repository->createFile('', $path .DIRECTORY_SEPARATOR. md5($test->getUri()) . DIRECTORY_SEPARATOR);
        $dirPath = $directory->getAbsolutePath().DIRECTORY_SEPARATOR;

        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0770, true);
        }

        if ($createTestFile === true) {
            $emptyTestXml = $this->getQtiTestTemplateFileAsString();

            $doc = new DOMDocument();
            $doc->loadXML($emptyTestXml);

            // Set the test label as title.
            $doc->documentElement->setAttribute('title', $test->getLabel());
            $doc->documentElement->setAttribute('identifier', str_replace('_', '-', tao_helpers_Display::textCleaner($test->getLabel(), '*', 32)));
            $doc->documentElement->setAttribute('toolVersion', TAO_VERSION);

            $filePath = $dirPath . TAOQTITEST_FILENAME;
            if ($doc->save($filePath) === false) {
                $msg = "Unable to write raw QTI Test template at location '${filePath}'.";
                throw new taoQtiTest_models_classes_QtiTestServiceException($msg, taoQtiTest_models_classes_QtiTestServiceException::TEST_WRITE_ERROR);
            }

            common_Logger::i("Created QTI Test content at location '" . $filePath . "'.");
        }

        $test->editPropertyValues(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $directory);
        return $directory;
    }

    /**
     * Delete the content of a QTI test
     * @param core_kernel_classes_Resource $test
     * @throws common_exception_Error
     */
    public function deleteContent(core_kernel_classes_Resource $test)
    {
        $content = $test->getOnePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));

        if (!is_null($content)) {
            $file = new core_kernel_file_File($content);

            try {
                $path = $file->getAbsolutePath();

                if (is_dir($path)) {
                    if (!tao_helpers_File::delTree($path)) {
                        throw new common_exception_Error("Unable to remove test content directory located at '" . $file->getAbsolutePath() . "'.");
                    }
                }
            }
            catch (common_Exception $e) {
                // Empty file...
            }

            $file->delete();
            $test->removePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $file);
        }
    }

    /**
     * Set the directory where the tests' contents are stored.
     * @param core_kernel_file_File $folder
     */
    public function setQtiTestDirectory(core_kernel_file_File $folder)
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
        $ext->setConfig(self::CONFIG_QTITEST_FOLDER, $folder->getUri());
    }

    /**
     * Get the directory where the tests' contents are stored.
     *
     * @return core_kernel_file_File
     * @throws common_Exception
     */
    public function getQtiTestDirectory()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
        $uri = $ext->getConfig(self::CONFIG_QTITEST_FOLDER);
        if (empty($uri)) {
            throw new common_Exception('No default repository defined for uploaded files storage.');
        }
        return new core_kernel_file_File($uri);
    }

    /**
     * Set the acceptable latency time (applied on qti:timeLimits->minTime, qti:timeLimits:maxTime).
     *
     * @param string $duration An ISO 8601 Duration.
     * @see http://www.php.net/manual/en/dateinterval.construct.php PHP's interval_spec format (based on ISO 8601).
     */
    public function setQtiTestAcceptableLatency($duration)
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
        $ext->setConfig(self::CONFIG_QTITEST_ACCEPTABLE_LATENCY, $duration);
    }

    /**
     * Get the acceptable latency time (applied on qti:timeLimits->minTime, qti:timeLimits->maxTime).
     *
     * @throws common_Exception If no value can be found as the acceptable latency in the extension's configuration file.
     * @return string An ISO 8601 Duration.
     * @see http://www.php.net/manual/en/dateinterval.construct.php PHP's interval_spec format (based on ISO 8601).
     */
    public function getQtiTestAcceptableLatency() {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
        $latency = $ext->getConfig(self::CONFIG_QTITEST_ACCEPTABLE_LATENCY);
        if (empty($latency)) {
            // Default duration for legacy code or missing config.
            return 'PT5S';
        }
        return $latency;
    }

    /**
     * Get the content of the QTI Test template file as an XML string.
     *
     * @return string|boolean The QTI Test template file content or false if it could not be read.
     */
    public function getQtiTestTemplateFileAsString()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
        return file_get_contents($ext->getDir() . 'models' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'qtiTest.xml');
    }
}
?>
