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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti;

use common_exception_Error;
use common_exception_UserReadableException;
use common_ext_ExtensionsManager;
use common_Logger;
use common_report_Report;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use core_kernel_versioning_Repository;
use DOMDocument;
use Exception;
use helpers_File;
use oat\tao\model\media\MediaService;
use oat\taoItems\model\media\ItemMediaResolver;
use oat\taoItems\model\media\LocalItemSource;
use oat\taoMediaManager\model\SharedStimulusImporter;
use oat\taoMediaManager\model\SharedStimulusPackageImporter;
use oat\taoQtiItem\helpers\Authoring;
use oat\taoQtiItem\model\apip\ApipService;
use oat\taoQtiItem\model\ItemModel;
use oat\taoQtiItem\model\qti\exception\ExtractException;
use oat\taoQtiItem\model\qti\exception\ParsingException;
use oat\taoQtiItem\model\qti\parser\ValidationException;
use oat\taoQtiItem\model\event\ItemImported;
use qtism\data\storage\xml\XmlStorageException;
use tao_helpers_File;
use tao_models_classes_GenerisService;
use taoItems_models_classes_ItemsService;
use oat\oatbox\event\EventManager;
use oat\oatbox\service\ServiceManager;

/**
 * Short description of class oat\taoQtiItem\model\qti\ImportService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 */
class ImportService extends tao_models_classes_GenerisService
{

    /**
     * Short description of method importQTIFile
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param $qtiFile
     * @param core_kernel_classes_Class $itemClass
     * @param bool $validate
     * @param core_kernel_versioning_Repository $repository unused
     * @throws \common_Exception
     * @throws \common_ext_ExtensionException
     * @throws common_exception_Error
     * @return common_report_Report
     */
    public function importQTIFile(
        $qtiFile,
        core_kernel_classes_Class $itemClass,
        $validate = true,
        core_kernel_versioning_Repository $repository = null,
        $extractApip = false
    ) {
        $report = null;

        try {

            $qtiModel = $this->createQtiItemModel($qtiFile, $validate);

            $rdfItem = $this->createRdfItem($itemClass, $qtiModel);

            // If APIP content must be imported, just extract the apipAccessibility element
            // and store it along the qti.xml file.
            if ($extractApip === true) {
                $this->storeApip($qtiFile, $rdfItem);
            }

            $report = \common_report_Report::createSuccess(__('The IMS QTI Item was successfully imported.'), $rdfItem);

        } catch (ValidationException $ve) {
            $report = \common_report_Report::createFailure(__('The IMS QTI Item could not be imported.'));
            $report->add($ve->getReport());
        }

        return $report;
    }

    /**
     *
     * @param core_kernel_classes_Class $itemClass
     * @param unknown $qtiModel
     * @throws common_exception_Error
     * @throws \common_Exception
     * @return core_kernel_classes_Resource
     */
    protected function createRdfItem(core_kernel_classes_Class $itemClass, $qtiModel)
    {
        $itemService = taoItems_models_classes_ItemsService::singleton();
        $qtiService = Service::singleton();

        if (!$itemService->isItemClass($itemClass)) {
            throw new common_exception_Error('provided non Itemclass for ' . __FUNCTION__);
        }

        $rdfItem = $itemService->createInstance($itemClass);

        //set the QTI type
        $itemService->setItemModel($rdfItem, new core_kernel_classes_Resource(ItemModel::MODEL_URI));

        //set the label
        $rdfItem->setLabel($qtiModel->getAttributeValue('title'));

        //save itemcontent
        if (!$qtiService->saveDataItemToRdfItem($qtiModel, $rdfItem)) {
            throw new \common_Exception('Unable to save item');
        }

        return $rdfItem;
    }

    protected function createQtiItemModel($qtiFile, $validate = true)
    {
        $qtiXml = Authoring::sanitizeQtiXml($qtiFile);
        //validate the file to import
        $qtiParser = new Parser($qtiXml);

        if ($validate) {
            $qtiParser->validate();

            if (!$qtiParser->isValid()) {
                $failedValidation = true;

                $eStrs = array();
                foreach ($qtiParser->getErrors() as $libXmlError) {
                    $eStrs[] = __('QTI-XML error at line %1$d "%2$s".', $libXmlError['line'],
                        str_replace('[LibXMLError] ', '', trim($libXmlError['message'])));
                }

                // Make sure there are no duplicate...
                $eStrs = array_unique($eStrs);

                // Add sub-report.
                throw new ValidationException($qtiFile, $eStrs);
            }
        }

        $qtiItem = $qtiParser->load();

        return $qtiItem;
    }

    protected function createQtiManifest($manifestFile, $validate = true)
    {
        //load and validate the manifest
        $qtiManifestParser = new ManifestParser($manifestFile);

        if ($validate) {
            $qtiManifestParser->validate();

            if (!$qtiManifestParser->isValid()) {

                $eStrs = array();
                foreach ($qtiManifestParser->getErrors() as $libXmlError) {
                    $eStrs[] = __('XML error at line %1$d "%2$s".', $libXmlError['line'],
                        str_replace('[LibXMLError] ', '', trim($libXmlError['message'])));
                }

                // Add sub-report.
                throw new ValidationException($manifestFile, $eStrs);
            }
        }

        return $qtiManifestParser->load();
    }

    private function storeApip($qtiFile, $rdfItem)
    {
        $originalDoc = new DOMDocument('1.0', 'UTF-8');
        $originalDoc->load($qtiFile);

        $apipService = ApipService::singleton();
        $apipService->storeApipAccessibilityContent($rdfItem, $originalDoc);
    }

    /**
     * imports a qti package and
     * returns the number of items imported
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param $file
     * @param core_kernel_classes_Class $itemClass
     * @param bool $validate
     * @param core_kernel_versioning_Repository $repository
     * @param bool $rollbackOnError
     * @param bool $rollbackOnWarning
     * @throws Exception
     * @throws ExtractException
     * @throws ParsingException
     * @throws \common_Exception
     * @throws \common_ext_ExtensionException
     * @throws common_exception_Error
     * @return common_report_Report
     */
    public function importQTIPACKFile(
        $file,
        core_kernel_classes_Class $itemClass,
        $validate = true,
        core_kernel_versioning_Repository $repository = null,
        $rollbackOnError = false,
        $rollbackOnWarning = false,
        $extractApip = false
    ) {

        //load and validate the package
        $qtiPackageParser = new PackageParser($file);

        if ($validate) {
            $qtiPackageParser->validate();
            if (!$qtiPackageParser->isValid()) {
                throw new ParsingException('Invalid QTI package format');
            }
        }

        //extract the package
        $folder = $qtiPackageParser->extract();
        if (!is_dir($folder)) {
            throw new ExtractException();
        }

        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, '');
        $successItems = array();
        try {

            // -- Initializing metadata services.
            $metadataMapping = Service::singleton()->getMetadataRegistry()->getMapping();
            $metadataInjectors = array();
            $metadataGuardians = array();
            $metadataClassLookups = array();
            $metadataValues = array();

            // The metadata import feature needs a DOM representation of the manifest.
            $domManifest = new DOMDocument('1.0', 'UTF-8');
            $domManifest->load($folder . 'imsmanifest.xml');

            foreach ($metadataMapping['injectors'] as $injector) {
                $metadataInjectors[] = new $injector();
                \common_Logger::i("Metadata Injector '${injector}' registered.");
            }

            foreach ($metadataMapping['guardians'] as $guardian) {
                $metadataGuardians[] = new $guardian();
                \common_Logger::i("Metadata Guardian '${guardian}' registered.");
            }

            foreach ($metadataMapping['classLookups'] as $classLookup) {
                $metadataClassLookups[] = new $classLookup();
                \common_Logger::i("Metadata Class Lookup '{$classLookup}' registered.");
            }

            foreach ($metadataMapping['extractors'] as $extractor) {
                $metadataExtractor = new $extractor();
                \common_Logger::i("Metatada Extractor '${extractor}' registered.");
                $metadataValues = array_merge($metadataValues, $metadataExtractor->extract($domManifest));
            }

            $metadataCount = count($metadataValues, COUNT_RECURSIVE);
            \common_Logger::i("${metadataCount} Metadata Values found in manifest by extractor(s).");

            $qtiItemResources = $this->createQtiManifest($folder . 'imsmanifest.xml');
            $itemCount = 0;
            $sharedFiles = array();
            foreach ($qtiItemResources as $qtiItemResource) {
                $itemCount++;
                $itemReport = $this->importQtiItem(
                    $folder,
                    $qtiItemResource,
                    $itemClass,
                    $extractApip,
                    array(),
                    $metadataValues,
                    $metadataInjectors,
                    $metadataGuardians,
                    $metadataClassLookups,
                    $sharedFiles
                );

                $rdfItem = $itemReport->getData();
                if ($rdfItem) {
                    $successItems[$qtiItemResource->getIdentifier()] = $rdfItem;
                }
                $report->add($itemReport);
            }
        } catch (ValidationException $ve) {
            $validationReport = \common_report_Report::createFailure("The IMS Manifest file could not be validated");
            $validationReport->add($ve->getReport());
            $report->setMessage(__("No Items could be imported from the given IMS QTI package."));
            $report->setType(common_report_Report::TYPE_ERROR);
            $report->add($validationReport);
        } catch (common_exception_UserReadableException $e) {
            $report = new common_report_Report(common_report_Report::TYPE_ERROR, __($e->getUserMessage()));
            $report->add($e);
        }

        if (!empty($successItems)) {
            // Some items were imported from the package.
            $report->setMessage(__('%d Item(s) of %d imported from the given IMS QTI Package.', count($successItems),
                $itemCount));

            if (count($successItems) !== $itemCount) {
                $report->setType(common_report_Report::TYPE_WARNING);
            }
        } else {
            $report->setMessage(__('No Items could be imported from the given IMS QTI package.'));
            $report->setType(common_report_Report::TYPE_ERROR);
        }

        if ($rollbackOnError === true) {
            if ($report->getType() === common_report_Report::TYPE_ERROR || $report->contains(common_report_Report::TYPE_ERROR)) {
                $this->rollback($successItems, $report);
            }
        } elseif ($rollbackOnWarning === true) {
            if ($report->getType() === common_report_Report::TYPE_WARNING || $report->contains(common_report_Report::TYPE_WARNING)) {
                $this->rollback($successItems, $report);
            }
        }

        // cleanup
        tao_helpers_File::delTree($folder);

        return $report;
    }


    /**
     * @param $folder
     * @param \oat\taoQtiItem\model\qti\Resource $qtiItemResource
     * @param $itemClass
     * @param bool|false $extractApip
     * @param array $dependencies
     * @param array $metadataValues
     * @param array $metadataInjectors
     * @param array $metadataGuardians
     * @param array $metadataClassLookups
     * @param array $sharedFiles
     * @return common_report_Report
     * @throws common_exception_Error
     */
    public function importQtiItem(
        $folder,
        Resource $qtiItemResource,
        $itemClass,
        $extractApip = false,
        array $dependencies = array(),
        array $metadataValues = array(),
        array $metadataInjectors = array(),
        array $metadataGuardians = array(),
        array $metadataClassLookups = array(),
        array &$sharedFiles = array()
    ) {

        try {
            //load the information about resources in the manifest

            $itemService = taoItems_models_classes_ItemsService::singleton();
            $qtiService = Service::singleton();

            $sources = MediaService::singleton()->getWritableSources();
            $sharedStorage = array_shift($sources);

            try {

                $resourceIdentifier = $qtiItemResource->getIdentifier();

                // Use the guardians to check whether or not the item has to be imported.
                foreach ($metadataGuardians as $guardian) {

                    if (isset($metadataValues[$resourceIdentifier]) === true) {
                        if (($guard = $guardian->guard($metadataValues[$resourceIdentifier])) !== false) {
                            \common_Logger::i("Resource '${resourceIdentifier}' is already stored in the database and will not be imported.");
                            $msg = __('The IMS QTI Item referenced as "%s" in the IMS Manifest file was already stored in the Item Bank.', $resourceIdentifier);
                            $report = common_report_Report::createInfo($msg, $guard);

                            // Simply do not import again.
                            return $report;
                        }
                    }
                }

                $targetClass = false;
                // Use the classLookups to determine where the item has to go.
                foreach ($metadataClassLookups as $classLookup) {
                    if (isset($metadataValues[$resourceIdentifier]) === true) {
                        \common_Logger::i("Target Class Lookup for resource '${resourceIdentifier}' ...");
                        if (($targetClass = $classLookup->lookup($metadataValues[$resourceIdentifier])) !== false) {
                            \common_Logger::i("Class Lookup Successful. Resource '${resourceIdentifier}' will be stored in RDFS Class '" . $targetClass->getUri() . "'.");
                            break;
                        }
                    }
                }

                $qtiFile = $folder . helpers_File::urlToPath($qtiItemResource->getFile());

                $qtiModel = $this->createQtiItemModel($qtiFile);
                $rdfItem = $this->createRdfItem((($targetClass !== false) ? $targetClass : $itemClass), $qtiModel);
                $name = $rdfItem->getLabel();
                $itemContent = $itemService->getItemContent($rdfItem);

                $xincluded = array();
                foreach ($qtiModel->getBody()->getComposingElements('oat\taoQtiItem\model\qti\Xinclude') as $xincludeEle) {
                    $xincluded[] = $xincludeEle->attr('href');
                    \common_Logger::i("Xinclude component found in resource '${resourceIdentifier}' with href '" . $xincludeEle->attr('href') . "'.");
                }

                $local = new LocalItemSource(array('item' => $rdfItem));

                foreach ($qtiItemResource->getAuxiliaryFiles() as $auxResource) {
                    // file on FS
                    $auxFile = $folder . str_replace('/', DIRECTORY_SEPARATOR, $auxResource);

                    // rel path in item
                    $auxPath = str_replace(DIRECTORY_SEPARATOR, '/', helpers_File::getRelPath($qtiFile, $auxFile));

                    if (!empty($sharedStorage) && in_array($auxPath, $xincluded)) {
                        $md5 = md5_file($auxFile);
                        if (isset($sharedFiles[$md5])) {
                            $info = $sharedFiles[$md5];
                            \common_Logger::i('Auxiliary file \'' . $auxPath . '\' linked to shared storage.');
                        } else {
                            // TODO cleanup sharedstimulus import/export
                            // move to taoQti item or library

                            // validate the shared stimulus
                            SharedStimulusImporter::isValidSharedStimulus($auxFile);
                            // embed assets in the shared stimulus
                            $newXmlFile = SharedStimulusPackageImporter::embedAssets($auxFile);
                            $info = $sharedStorage->add($newXmlFile, basename($auxFile), $name);
                            if (method_exists($sharedStorage, 'forceMimeType')) {
                                // add() does not return link, so we need to parse it
                                $resolver = new ItemMediaResolver($rdfItem, '');
                                $asset = $resolver->resolve($info['uri']);
                                $sharedStorage->forceMimeType($asset->getMediaIdentifier(), 'application/qti+xml');
                            }
                            $sharedFiles[$md5] = $info;
                            \common_Logger::i('Auxiliary file \'' . $auxPath . '\' added to shared storage.');
                        }
                    } else {
                        // store locally, in a safe directory
                        $safePath = '';
                        if (dirname($auxPath) !== '.') {
                            $safePath = str_replace('../', '', dirname($auxPath)) . '/';
                        }
                        $info = $local->add($auxFile, basename($auxFile), $safePath);
                        \common_Logger::i('Auxiliary file \'' . $auxPath . '\' copied.');
                    }

                    // replace uri if changed
                    if ($auxPath != ltrim($info['uri'], '/')) {
                        $itemContent = str_replace($auxPath, $info['uri'], $itemContent);
                    }
                }

                foreach ($qtiItemResource->getDependencies() as $dependency) {
                    // file on FS
                    if (isset($dependencies[$dependency])) {
                        $auxFile = $dependencies[$dependency]->getFile();

                        $auxFile = $folder . str_replace('/', DIRECTORY_SEPARATOR, $auxFile);

                        // rel path in item
                        $auxPath = str_replace(DIRECTORY_SEPARATOR, '/', helpers_File::getRelPath($qtiFile, $auxFile));

                        if (!empty($sharedStorage) && in_array($auxPath, $xincluded)) {
                            $md5 = md5_file($auxFile);
                            if (isset($sharedFiles[$md5])) {
                                $info = $sharedFiles[$md5];
                                \common_Logger::i('Auxiliary file \'' . $auxPath . '\' linked to shared storage.');
                            } else {
                                // TODO cleanup sharedstimulus import/export
                                // move to taoQti item or library

                                // validate the shared stimulus
                                SharedStimulusImporter::isValidSharedStimulus($auxFile);
                                // embed assets in the shared stimulus
                                $newXmlFile = SharedStimulusPackageImporter::embedAssets($auxFile);
                                $info = $sharedStorage->add($newXmlFile, basename($auxFile), $name);
                                if (method_exists($sharedStorage, 'forceMimeType')) {
                                    // add() does not return link, so we need to parse it
                                    $resolver = new ItemMediaResolver($rdfItem, '');
                                    $asset = $resolver->resolve($info['uri']);
                                    $sharedStorage->forceMimeType($asset->getMediaIdentifier(), 'application/qti+xml');
                                }
                                $sharedFiles[$md5] = $info;
                                \common_Logger::i('Auxiliary file \'' . $auxPath . '\' added to shared storage.');
                            }
                        } else {
                            // store locally, in a safe directory
                            $safePath = '';
                            if (dirname($auxPath) !== '.') {
                                $safePath = str_replace('../', '', dirname($auxPath)) . '/';
                            }
                            $info = $local->add($auxFile, basename($auxFile), $safePath);
                            \common_Logger::i('Auxiliary file \'' . $auxPath . '\' copied.');
                        }

                        // replace uri if changed
                        if ($auxPath != ltrim($info['uri'], '/')) {
                            $itemContent = str_replace($auxPath, $info['uri'], $itemContent);
                        }


                    }
                }

                $itemService->setItemContent($rdfItem, $itemContent);

                // Finally, import metadata.
                $this->importItemMetadata($metadataValues, $qtiItemResource, $rdfItem, $metadataInjectors);

                // And Apip if wanted
                if ($extractApip) {
                    $this->storeApip($qtiFile, $rdfItem);
                }

                $eventManager = ServiceManager::getServiceManager()->get(EventManager::CONFIG_ID);
                $eventManager->trigger(new ItemImported($rdfItem, $qtiModel));

                $msg = __('The IMS QTI Item referenced as "%s" in the IMS Manifest file was successfully imported.',
                    $qtiItemResource->getIdentifier());
                $report = common_report_Report::createSuccess($msg, $rdfItem);

            } catch (ParsingException $e) {
                $report = new common_report_Report(common_report_Report::TYPE_ERROR, $e->getUserMessage());
            } catch (ValidationException $ve) {
                $report = \common_report_Report::createFailure(__('IMS QTI Item referenced as "%s" in the IMS Manifest file could not be imported.',
                    $qtiItemResource->getIdentifier()));
                $report->add($ve->getReport());
            } catch (XmlStorageException $e){

                $files = array();
                $message = __('There are errors in the following shared stimulus : ').PHP_EOL;
                /** @var \LibXMLError $error */
                foreach($e->getErrors() as $error){
                    if(!in_array($error->file, $files)){
                        $files[] = $error->file;
                        $message .= '- '.basename($error->file).' :'.PHP_EOL;
                    }
                    $message .= $error->message.' at line : '.$error->line.PHP_EOL;
                }

                $report = new common_report_Report(common_report_Report::TYPE_ERROR,
                    $message);
            } catch (Exception $e) {
                // an error occured during a specific item
                $report = new common_report_Report(common_report_Report::TYPE_ERROR,
                    __("An unknown error occured while importing the IMS QTI Package."));
                common_Logger::e($e->getMessage());
            }
        } catch (ValidationException $ve) {
            $validationReport = \common_report_Report::createFailure("The IMS Manifest file could not be validated");
            $validationReport->add($ve->getReport());
            $report->setMessage(__("No Items could be imported from the given IMS QTI package."));
            $report->setType(common_report_Report::TYPE_ERROR);
            $report->add($validationReport);
        } catch (common_exception_UserReadableException $e) {
            $report = new common_report_Report(common_report_Report::TYPE_ERROR, __($e->getUserMessage()));
            $report->add($e);
        }

        return $report;

    }

    /**
     * Import metadata to a given QTI Item.
     *
     * @param oat\taoQtiItem\model\qti\metadata\MetadataValue[] $metadataValues An array of MetadataValue objects.
     * @param Resource $qtiResource The object representing the QTI Resource, from an IMS Manifest perspective.
     * @param core_kernel_classes_Resource $resource The object representing the target QTI Item in the Ontology.
     * @param oat\taoQtiItem\model\qti\metadata\MetadataInjector[] $ontologyInjectors Implementations of MetadataInjector that will take care to inject the metadata values in the appropriate Ontology Resource Properties.
     * @throws oat\taoQtiItem\model\qti\metadata\MetadataInjectionException If an error occurs while importing the metadata.
     */
    public function importItemMetadata(
        array $metadataValues,
        Resource $qtiResource,
        core_kernel_classes_Resource $resource,
        array $ontologyInjectors = array()
    ) {
        // Filter metadata values for this given item.
        $identifier = $qtiResource->getIdentifier();
        if (isset($metadataValues[$identifier]) === true) {
            \common_Logger::i("Preparing Metadata Values for resource '${identifier}'...");
            $values = $metadataValues[$identifier];

            foreach ($ontologyInjectors as $injector) {
                $valuesCount = count($values);
                $injectorClass = get_class($injector);
                \common_Logger::i("Attempting to inject ${valuesCount} Metadata Values in database for resource '${identifier}' with Metadata Injector '${injectorClass}'.");
                $injector->inject($resource, array($identifier => $values));
            }
        }
    }

    /**
     * @param array $items
     * @param common_report_Report $report
     * @throws common_exception_Error
     */
    protected function rollback(array $items, common_report_Report $report)
    {
        foreach ($items as $id => $item) {
            @taoItems_models_classes_ItemsService::singleton()->deleteItem($item);
            $report->add(new common_report_Report(common_report_Report::TYPE_WARNING,
                __('The IMS QTI Item referenced as "%s" in the IMS Manifest was successfully rolled back.', $id)));
        }
    }
}
