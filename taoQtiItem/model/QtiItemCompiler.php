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
 */

namespace oat\taoQtiItem\model;

use taoItems_models_classes_ItemCompiler;
use core_kernel_classes_Resource;
use tao_models_classes_service_StorageDirectory;
use tao_models_classes_service_ServiceCall;
use tao_models_classes_service_ConstantParameter;
use taoItems_models_classes_ItemsService;
use taoItems_helpers_Deployment;
use common_report_Report;
use common_ext_ExtensionsManager;
use common_Logger;
use oat\taoQtiItem\model\qti\Service;
use tao_helpers_File;
use qtism\data\storage\xml\XmlAssessmentItemDocument;

/**
 * The QTI Item Compiler
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems

 */
class QtiItemCompiler extends taoItems_models_classes_ItemCompiler
{
    /**
     * instance representing the service to run the QTI item
     * @var string
     */
    const INSTANCE_ITEMRUNNER = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ServiceQtiItemRunner';
    
    /**
     * Compile qti item
     * 
     * @throws taoItems_models_classes_CompilationFailedException
     * @return tao_models_classes_service_ServiceCall
     */
    public function compile() {

        $publicDirectory = $this->spawnPublicDirectory();
        $privateDirectory = $this->spawnPrivateDirectory();
        $item = $this->getResource();

        $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, __('Published %s', $item->getLabel()));
        if(!taoItems_models_classes_ItemsService::singleton()->isItemModelDefined($item)){
            return $this->fail(__('Item \'%s\' has no model', $item->getLabel()));
        }

        $langs = $this->getContentUsedLanguages();
        if (empty($langs)) {
            $report->setType(common_report_Report::TYPE_ERROR);
            $report->setMessage(__('Item "%s" is not available in any language', $item->getLabel()));
        }
        foreach($langs as $compilationLanguage){
            $publicLangDir = $this->getLanguageCompilationPath($publicDirectory, $compilationLanguage);
            if(!is_dir($publicLangDir)){
                if(!@mkdir($publicLangDir)){
                    common_Logger::e('Could not create directory '.$publicLangDir, 'COMPILER');
                    return $this->fail(__('Could not create language specific directory for item \'%s\'', $item->getLabel()));
                }
            }

            $privateLangDir = $this->getLanguageCompilationPath($privateDirectory, $compilationLanguage);
            if(!is_dir($privateLangDir)){
                if(!@mkdir($privateLangDir)){
                    common_Logger::e('Could not create directory '.$privateLangDir, 'COMPILER');
                    return $this->fail(__('Could not create language specific directory for item \'%s\'', $item->getLabel()));
                }
            }


            $langReport = $this->deployQtiItem($item, $compilationLanguage, $publicLangDir, $privateLangDir);
            $report->add($langReport);
            if ($langReport->getType() == common_report_Report::TYPE_ERROR) {
                $report->setType(common_report_Report::TYPE_ERROR);
                $report->setMessage(__('Failed to publish %1$s in %2$s', $item->getLabel(), $compilationLanguage));
                break;
            }
        }
        if($report->getType() == common_report_Report::TYPE_SUCCESS){
            $report->setData($this->createQtiService($item, $publicDirectory, $privateDirectory));
        }
        return $report;
    }

    /**
     * Create a servicecall that runs the prepared qti item
     * 
     * @param core_kernel_classes_Resource $item
     * @param tao_models_classes_service_StorageDirectory $publicDirectory
     * @param tao_models_classes_service_StorageDirectory $privateDirectory
     * @return tao_models_classes_service_ServiceCall
     */
    protected function createQtiService(core_kernel_classes_Resource $item, tao_models_classes_service_StorageDirectory $publicDirectory, tao_models_classes_service_StorageDirectory $privateDirectory){

        $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(self::INSTANCE_ITEMRUNNER));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
                new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMPATH), $publicDirectory->getId()
        ));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
                new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMDATAPATH), $privateDirectory->getId()
        ));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
                new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMURI), $item
        ));

        return $service;
    }

    /**
     * Desploy all the required files into the provided directories
     * 
     * @param core_kernel_classes_Resource $item
     * @param string $language
     * @param string $publicDirectory
     * @param string $privateFolder
     * @return common_report_Report
     */
    protected function deployQtiItem(core_kernel_classes_Resource $item, $language, $publicDirectory, $privateFolder){

//        start debugging here
        common_Logger::d('destination original '.$publicDirectory.' '.$privateFolder);

        $itemService = taoItems_models_classes_ItemsService::singleton();
        $qtiService = Service::singleton();

        //copy all item folder (but the qti.xml)
        $itemFolder = $itemService->getItemFolder($item, $language);
        taoItems_helpers_Deployment::copyResources($itemFolder, $publicDirectory, array('qti.xml'));

        //copy item.xml file to private directory
        tao_helpers_File::copy($itemFolder.'qti.xml', $privateFolder.'qti.xml', false);
       

        //copy client side resources (javascript loader)
        $qtiItemDir = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getDir();
        $taoDir = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->getDir();
        $assetPath      = $qtiItemDir . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
        $assetLibPath   = $taoDir     . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;
        if(\tao_helpers_Mode::is('production')){
            tao_helpers_File::copy( $assetPath . 'qtiLoader.min.js', $publicDirectory.'qtiLoader.min.js', false);
        } else {
            tao_helpers_File::copy( $assetPath . 'qtiLoader.js', $publicDirectory.'qtiLoader.js', false);
            tao_helpers_File::copy( $assetLibPath . 'require.js', $publicDirectory.'require.js', false);
        }

        //load item in php object : 
        //warning:  use the same instance of php qti item because the serial is regenerated each time:
        $qtiItem = $qtiService->getDataItemByRdfItem($item, $language);
        
        //store variable qti elements data into the private directory
        $variableElements = $qtiService->getVariableElements($qtiItem);
        $serializedVariableElements = json_encode($variableElements);
        file_put_contents($privateFolder.'variableElements.json', $serializedVariableElements);

        // render item
        $xhtml = $qtiService->renderQTIItem($qtiItem, $language);

        // retrieve external resources
        $report = taoItems_helpers_Deployment::retrieveExternalResources($xhtml, $publicDirectory);//@todo (optional) : exclude 'require.js' from copying
        if($report->getType() == common_report_Report::TYPE_SUCCESS){
            $xhtml = $report->getData();
        }else{
            return $report;
        }

        //note : no need to manually copy qti or other third party lib files, all dependencies are managed by requirejs
        // write index.html
        file_put_contents($publicDirectory.'index.html', $xhtml);

        //copy the event.xml if not present
        $eventsXmlFile = $publicDirectory.'events.xml';
        if(!file_exists($eventsXmlFile)){
            $qtiItemDir = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getDir();
            $eventsReference = $qtiItemDir.'model'.DIRECTORY_SEPARATOR.'qti'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'events_ref.xml';
            $eventXml = file_get_contents($eventsReference);
            if(is_string($eventXml) && !empty($eventXml)){
                $eventXml = str_replace('{ITEM_URI}', $item->getUri(), $eventXml);
                @file_put_contents($eventsXmlFile, $eventXml);
            }
        }

        // --- Include QTI specific compilation stuff here.
        // At this moment, we should have compiled raw Items. We now have
        // to put the qti.xml file for each language in the compilation folder.

        return new common_report_Report(
                common_report_Report::TYPE_SUCCESS, __('Successfully compiled "%s"', $language)
        );
    }

}
