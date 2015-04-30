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

use oat\taoQtiItem\model\qti\exception\ParsingException;
use oat\taoQtiItem\model\qti\exception\ExtractException;
use \tao_models_classes_GenerisService;
use \core_kernel_classes_Class;
use \core_kernel_classes_Resource;
use \core_kernel_versioning_Repository;
use \common_report_Report;
use \taoItems_models_classes_ItemsService;
use \common_exception_Error;
use \common_ext_ExtensionsManager;
use \core_kernel_classes_Property;
use \tao_models_classes_Parser;
use \tao_helpers_File;
use \helpers_File;
use \Exception;
use \DOMDocument;
use \common_exception_UserReadableException;
use \common_Logger;
use oat\taoQtiItem\model\ItemModel;

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
     * @param core_kernel_versioning_Repository $repository
     * @throws \common_Exception
     * @throws \common_ext_ExtensionException
     * @throws common_exception_Error
     * @return common_report_Report
     */
    public function importQTIFile($qtiFile, core_kernel_classes_Class $itemClass, $validate = true, core_kernel_versioning_Repository $repository = null)
    {
        $returnValue = null;

        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, 'The IMS QTI Item was successfully imported.');
        
        //repository
        $repository = is_null($repository) ? taoItems_models_classes_ItemsService::singleton()->getDefaultFileSource() : $repository;

        //get the services instances we will need
        $itemService = taoItems_models_classes_ItemsService::singleton();
        $qtiService = Service::singleton();

        if (!$itemService->isItemClass($itemClass)) {
            throw new common_exception_Error('provided non Itemclass for '.__FUNCTION__);
        }

        //validate the file to import
        $qtiParser = new Parser($qtiFile);
        $valid = true;
        
        if ($validate) {
            $basePath = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getDir();
            $this->validateMultiple($qtiParser, array(
                $basePath.'model/qti/data/qtiv2p1/imsqti_v2p1.xsd',
                $basePath.'model/qti/data/qtiv2p0/imsqti_v2p0.xsd',
                $basePath.'model/qti/data/apipv1p0/Core_Level/Package/apipv1p0_qtiitemv2p1_v1p0.xsd'
            ));
            
            if (!$qtiParser->isValid()) {
                $valid = false;
                $eStrs = array();
                
                foreach ($qtiParser->getErrors() as $libXmlError) {
                    $eStrs[] = __('QTI-XML error at line %1$d "%2$s".', $libXmlError['line'], str_replace('[LibXMLError] ', '', trim($libXmlError['message'])));
                }
                
                // Make sure there are no duplicate...
                $eStrs = array_unique($eStrs);
                
                // Add sub-report.
                $report->add(common_report_Report::createFailure(__("Malformed XML:\n%s", implode("\n", $eStrs))));
            }
        }
        
        if ($valid) {
            //load the QTI item from the file
            $qtiItem = $qtiParser->load();
            
            //create the instance
            // @todo add type and repository
            $rdfItem = $itemService->createInstance($itemClass);
            
            //set the QTI type
            $itemService->setItemModel($rdfItem, new core_kernel_classes_Resource(ItemModel::MODEL_URI));
            
            //set the label
            $rdfItem->setLabel($qtiItem->getAttributeValue('title'));
            
            //save itemcontent
            if($qtiService->saveDataItemToRdfItem($qtiItem, $rdfItem)){
                $returnValue = $rdfItem;
            }
        }
        
        if ($report->containsError() === true) {
            $report->setType(common_report_Report::TYPE_ERROR);
            $report->setMessage(__('The IMS QTI Item could not be imported.'));
        }

        $report->setData($returnValue);
        
        return $report;
    }

    /**
     * Excecute parser validation and stops at the first valid one, and returns the identified schema
     * 
     * @param tao_models_classes_Parser $parser
     * @param array $xsds
     * @return string
     */
    public function validateMultiple(tao_models_classes_Parser $parser, $xsds = array())
    {
        $returnValue = '';

        foreach ($xsds as $xsd) {
            $parser->validate($xsd);
            if ($parser->isValid()) {
                $returnValue = $xsd;
                break;
            }
        }

        return $returnValue;
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
    public function importQTIPACKFile($file, core_kernel_classes_Class $itemClass, $validate = true, core_kernel_versioning_Repository $repository = null, $rollbackOnError = false, $rollbackOnWarning = false)
    {
        //repository
        $repository = is_null($repository) ? taoItems_models_classes_ItemsService::singleton()->getDefaultFileSource() : $repository;

        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, '');
        
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

        //load and validate the manifest
        $qtiManifestParser = new ManifestParser($folder.'imsmanifest.xml');
        
        if ($validate) {
            $basePath = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getDir();
            $this->validateMultiple($qtiManifestParser, array(
                $basePath.'models/classes/QTI/data/imscp_v1p1.xsd',
                $basePath.'models/classes/QTI/data/apipv1p0/Core_Level/Package/apipv1p0_imscpv1p2_v1p0.xsd'
            ));
            
            if(!$qtiManifestParser->isValid()) {
                tao_helpers_File::delTree($folder);
                
                $eStrs = array();
                foreach ($qtiManifestParser->getErrors() as $libXmlError) {
                    $eStrs[] = __('XML error at line %1$d "%2$s".', $libXmlError['line'], str_replace('[LibXMLError] ', '', trim($libXmlError['message'])));
                }
                
                $report->add(new common_report_Report(common_report_Report::TYPE_ERROR, __("The IMS Manifest file could not be validated:\n%s", implode($eStrs, "\n"))));
                
                $report->setType(common_report_Report::TYPE_ERROR);
                $report->setMessage(__("No Items could be imported from the given IMS QTI package."));
                
                return $report;
            }
        }

        try {
            //load the information about resources in the manifest 
            $qtiItemResources = $qtiManifestParser->load();
            $itemService = taoItems_models_classes_ItemsService::singleton();
            $qtiService = Service::singleton();
            
            // The metadata import feature needs a DOM representation of the manifest.
            $domManifest = new DOMDocument('1.0', 'UTF-8');
            $domManifest->load($qtiManifestParser->getSource());
            $metadataMapping = $qtiService->getMetadataRegistry()->getMapping();
            $metadataInjectors = array();
            $metadataValues = array();
            
            foreach ($metadataMapping['injectors'] as $injector) {
                $metadataInjectors[] = new $injector();
            }
            
            foreach ($metadataMapping['extractors'] as $extractor) {
                $metadataExtractor = new $extractor();
                $metadataValues = array_merge($metadataValues, $metadataExtractor->extract($domManifest));
            }
            
            $successItems = array();
            $successCount = 0;
            $itemCount = 0;
            
            foreach ($qtiItemResources as $qtiItemResource) {
                
                $itemCount++;
                
                try {
                    $qtiFile = $folder . $qtiItemResource->getFile();
                    $itemReport = $this->importQTIFile($qtiFile, $itemClass, $validate, $repository);
                    $rdfItem = $itemReport->getData();
                    
                    if ($rdfItem) {
                        $itemPath = taoItems_models_classes_ItemsService::singleton()->getItemFolder($rdfItem);
                        $itemContent = $itemService->getItemContent($rdfItem);
                        
                        foreach ($qtiItemResource->getAuxiliaryFiles() as $auxResource) {
                            // $auxResource is a relativ URL, so we need to replace the slashes with directory separators
                            $auxPath = $folder.str_replace('/', DIRECTORY_SEPARATOR, $auxResource);
                            $relPath = helpers_File::getRelPath($qtiFile, $auxPath);
                        
                            //prevent directory traversal:
                            $relPathSafe = str_replace('..'.DIRECTORY_SEPARATOR, '', $relPath, $count);
                            if($count){
                                $itemContent = str_replace($relPath, $relPathSafe, $itemContent);
                            }
                        
                            $destPath = $itemPath.$relPathSafe;
                            tao_helpers_File::copy($auxPath, $destPath, true);
                        }
                        
                        // Finally, import metadata.
                        $this->importItemMetadata($metadataValues, $qtiItemResource, $rdfItem, $metadataInjectors);
                        
                        $itemService->setItemContent($rdfItem, $itemContent);
                        $successItems[$qtiItemResource->getIdentifier()] = $rdfItem;
                        $successCount++;
                    }
                    
                    // Modify the message of the item report to include more specific
                    // information e.g. the item identifier.
                    if ($itemReport->containsError() === false) {
                        $itemReport->setMessage(__('The IMS QTI Item referenced as "%s" in the IMS Manifest file was successfully imported.', $qtiItemResource->getIdentifier()));
                    } else {
                        $itemReport->setMessage(__('The IMS QTI Item referenced as "%s" in the IMS Manifest file could not be imported.', $qtiItemResource->getIdentifier()));
                    }
                    
                    $report->add($itemReport);
                    
                } catch (ParsingException $e) {
                    $report->add(new common_report_Report(common_report_Report::TYPE_ERROR, $e->getUserMessage()));
                } catch (Exception $e) {
                    // an error occured during a specific item
                    $report->add(new common_report_Report(common_report_Report::TYPE_ERROR, __("An unknown error occured while importing the IMS QTI Package.")));
                    common_Logger::e($e->getMessage());
                }
            }
            
            if ($successCount > 0) {
                // Some items were imported from the package.
                $report->setMessage(__('%d Item(s) of %d imported from the given IMS QTI Package.', $successCount, $itemCount));
                
                if ($successCount !== $itemCount) {
                    $report->setType(common_report_Report::TYPE_WARNING);
                }
            }
            else {
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
            
        } catch (common_exception_UserReadableException $e) {
            $report = new common_report_Report(common_report_Report::TYPE_ERROR, __($e->getUserMessage()));
            $report->add($e);
        }

        // cleanup
        tao_helpers_File::delTree($folder);

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
    protected function importItemMetadata(array $metadataValues, Resource $qtiResource, core_kernel_classes_Resource $resource, array $ontologyInjectors = array())
    {
        // Filter metadata values for this given item.
        $identifier = $qtiResource->getIdentifier();
        if (isset($metadataValues[$identifier]) === true) {
            
            $values = $metadataValues[$identifier];
            
            foreach ($ontologyInjectors as $injector) {
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
            $report->add(new common_report_Report(common_report_Report::TYPE_WARNING, __('The IMS QTI Item referenced as "%s" in the IMS Manifest was successfully rolled back.', $id)));
        }
    }
}