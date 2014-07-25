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
use qtism\data\storage\xml\XmlAssessmentItemDocument;

/**
 * The QTI Item Compiler
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes
 */
class taoQTI_models_classes_QtiItemCompiler extends taoItems_models_classes_ItemCompiler
{

    protected function createService(core_kernel_classes_Resource $item, core_kernel_file_File $destinationDirectory){
        $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_QTI_SERVICE_ITEMRUNNER));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
                new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMPATH), $destinationDirectory
        ));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
                new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMURI), $item
        ));

        return $service;
    }

    protected function deployItem(core_kernel_classes_Resource $item, $language, $destination){
        $itemService = taoItems_models_classes_ItemsService::singleton();

        // copy local files
        $source = $itemService->getItemFolder($item, $language);
        // (dev's note) The qti.xml file will be embedded later in a private folder.
        taoItems_helpers_Deployment::copyResources($source, $destination, array('qti.xml'));

        // render item
        $xhtml = $itemService->render($item, $language);

        // retrieve external resources
        $xhtml = taoItems_helpers_Deployment::retrieveExternalResources($xhtml, $destination);

        // add qti files
        $taoQTIext = common_ext_ExtensionsManager::singleton()->getExtensionByID('taoQTI');
        taoItems_helpers_Deployment::copyResources(
                $taoQTIext->getConstant('BASE_PATH').'views/js/qtiDefaultRenderer/css/img/', $destination.'img'.DIRECTORY_SEPARATOR
        );
        taoItems_helpers_Deployment::copyResources(
                TAOVIEW_PATH.'css/custom-theme/images/', $destination.'images'.DIRECTORY_SEPARATOR
        );

        //add required (heavy) libs
        if(strpos($xhtml, 'mediaelement-and-player.min.js')){
            taoItems_helpers_Deployment::copyResources(
                    $taoQTIext->getConstant('BASE_PATH').'views/js/qtiDefaultRenderer/lib/mediaelement/css/', $destination
            );
            tao_helpers_File::copy($taoQTIext->getConstant('BASE_PATH').'views/js/qtiDefaultRenderer/lib/mediaelement/flashmediaelement.swf', $destination.'flashmediaelement.swf', true);
        }

        if(strpos($xhtml, 'MathJax.js')){
            taoItems_helpers_Deployment::copyResources($taoQTIext->getConstant('BASE_PATH').'views/js/mathjax/', $destination);
        }

        // write index.html
        file_put_contents($destination.'index.html', $xhtml);

        //copy the event.xml if not present
        $eventsXmlFile = $destination.'events.xml';
        if(!file_exists($eventsXmlFile)){
            $eventXml = file_get_contents(ROOT_PATH.'/taoItems/data/events_ref.xml');
            if(is_string($eventXml) && !empty($eventXml)){
                $eventXml = str_replace('{ITEM_URI}', $item->getUri(), $eventXml);
                @file_put_contents($eventsXmlFile, $eventXml);
            }
        }

        // --- Include QTI specific compilation stuff here.
        // At this moment, we should have compiled raw Items. We now have
        // to put the qti.xml file for each language in the compilation folder.

        return true;
    }

}