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
 * 
 */
namespace oat\taoQtiItem\controller;

//use oat\taoQtiItem\controller\QtiPreview;
use oat\taoQtiItem\helpers\QtiFile;
use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiItem\model\qti\Item;
use \taoItems_actions_ItemPreview;
use \tao_helpers_Uri;
use \core_kernel_classes_Resource;
use \common_Exception;
use \taoQtiCommon_helpers_PciVariableFiller;
use \common_Logger;
use \taoQtiCommon_helpers_ResultTransmissionException;
use \taoQtiCommon_helpers_PciStateOutput;
use \taoQtiCommon_helpers_Utils;
use \common_ext_ExtensionsManager;
use qtism\runtime\common\State;
use qtism\runtime\tests\SessionManager;
use qtism\runtime\tests\AssessmentItemSession;
use qtism\runtime\tests\AssessmentItemSessionException;
use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\StorageException;

/**
 * Qti Item Runner Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoQTI
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class QtiPreview extends taoItems_actions_ItemPreview
{

    public function getPreviewUrl($item, $options = array()){
        $code = base64_encode($item->getUri());
        return _url('render/'.$code.'/index', 'QtiPreview', 'taoQtiItem', $options);
    }

    public function submitResponses(){

        $itemUri = tao_helpers_Uri::decode($this->getRequestParameter('itemUri'));

        if (!empty($itemUri)) {
            $this->processResponses(new core_kernel_classes_Resource($itemUri));
        }
        else {
            throw new common_Exception('missing required itemUri');
        }
    }

    /**
     * Item's ResponseProcessing.
     *
     * @param core_kernel_classes_Resource $item The Item you want to apply ResponseProcessing.
     * @throws \RuntimeException If an error occurs while processing responses or transmitting results
     */
    protected function processResponses(core_kernel_classes_Resource $item){
        
        $jsonPayload = taoQtiCommon_helpers_Utils::readJsonPayload();
        
        try {
            $qtiXmlFilePath = QtiFile::getQtiFilePath($item);
            $qtiXmlDoc = new XmlDocument();
            $qtiXmlDoc->load($qtiXmlFilePath);
        }
        catch(StorageException $e) {
            $msg = "An error occurred while loading QTI-XML file at expected location '${qtiXmlFilePath}'.";
            common_Logger::e($e->getPrevious()->getMessage());
            throw new \RuntimeException($msg, 0, $e);
        }

        $itemSession = new AssessmentItemSession($qtiXmlDoc->getDocumentComponent(), new SessionManager());
        $itemSession->beginItemSession();

        $variables = array();
        $filler = new taoQtiCommon_helpers_PciVariableFiller($qtiXmlDoc->getDocumentComponent());
        
        // Convert client-side data as QtiSm Runtime Variables.
        foreach ($jsonPayload as $id => $response) {
            
            try {
                $var  = $filler->fill($id, $response);
                // Do not take into account QTI Files at preview time.
                // Simply delete the created file.
                if (taoQtiCommon_helpers_Utils::isQtiFile($var, false) === true) {
                    $fileManager = taoQtiCommon_helpers_Utils::getFileDatatypeManager();
                    $fileManager->delete($var->getValue());
                }
                else {
                    $variables[] = $var;
                }
            }
            catch (OutOfRangeException $e) {
                // A variable value could not be converted, ignore it.
                // Developer's note: QTI Pairs with a single identifier (missing second identifier of the pair) are transmitted as an array of length 1,
                // this might cause problem. Such "broken" pairs are simply ignored.
                common_Logger::d("Client-side value for variable '${id}' is ignored due to data malformation.");
            }
            catch (OutOfBoundsException $e) {
                // No such identifier found in item.
                common_Logger::d("The variable with identifier '${id}' is not declared in the item definition.");
            }
        }

        try {
            $itemSession->beginAttempt();
            $itemSession->endAttempt(new State($variables));

            // Return the item session state to the client-side.
            echo json_encode(array('success' => true, 'displayFeedback' => true, 'itemSession' => self::buildOutcomeResponse($itemSession)));
        }
        catch(AssessmentItemSessionException $e) {
            $msg = "An error occurred while processing the responses.";
            throw new \RuntimeException($msg, 0, $e);
        }
        catch(taoQtiCommon_helpers_ResultTransmissionException $e) {
            $msg = "An error occurred while transmitting a result to the target Result Server.";
            throw new \RuntimeException($msg, 0, $e);
        }
    }

    /**
     * Get the ResultServer API call to be used by the item.
     *
     * @return string A string representing JavaScript instructions.
     */
    protected function getResultServer() {
        $itemUri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
        return array(
            'module'    => 'taoQtiItem/QtiPreviewResultServerApi',
            'endpoint'  => ROOT_URL . 'taoQtiItem/QtiPreview/',
            'params'    => $itemUri
        );
    }


    protected static function buildOutcomeResponse(AssessmentItemSession $itemSession) {
        $stateOutput = new taoQtiCommon_helpers_PciStateOutput();

        foreach ($itemSession->getOutcomeVariables(false) as $var) {
            $stateOutput->addVariable($var);
        }

        $output = $stateOutput->getOutput();
        return $output;
    }

    /**
     * (non-PHPdoc)
     * @see taoItems_actions_ItemPreview::getRenderedItem()
     */
    protected function getRenderedItem($item) {
        
        $qtiItem = Service::singleton()->getDataItemByRdfItem($item);
        
        $contentVariableElements = array_merge($this->getModalFeedbacks($qtiItem), $this->getRubricBlocks($qtiItem));
        
        $taoBaseUrl = common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->getConstant('BASE_WWW');
        $qtiBaseUrl = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getConstant('BASE_WWW');
        
        $taoLibUrl = $taoBaseUrl.'js/lib/';
        $taoQtiItemLibUrl = $qtiBaseUrl.'js/runtime/';
        
        $xhtml = $qtiItem->toXHTML(array(
            'contentVariableElements' => $contentVariableElements,
          //  'js' => array($qtiBaseUrl.'js/preview/qtiViewSelector.js'),
            'js_var' => array('view' => $this->getRequestView()),
           // 'css' => array($qtiBaseUrl.'css/preview/qtiViewSelector.css'),
            'path' => array(
                'tao' => $taoLibUrl,
                'taoQtiItem' => $taoQtiItemLibUrl
            )
        ));

        return $xhtml;
    }

    protected function getRequestView() {
        $returnValue = 'candidate';
        
        if ($this->hasRequestParameter('view')) {
            $returnValue = tao_helpers_Uri::decode($this->getRequestParameter('view'));
        }
        
        return $returnValue;
    }

    protected function getRubricBlocks(Item $qtiItem){

        $returnValue = array();

        $currentView = $this->getRequestView();
        $rubricBlocks = $qtiItem->getRubricBlocks();
        
        foreach($rubricBlocks as $rubricBlock) {
            
            $view = $rubricBlock->attr('view');
            
            if (!empty($view) && in_array($currentView, $view)) {
                $returnValue[$rubricBlock->getSerial()] = $rubricBlock->toArray();
            }
        }

        return $returnValue;
    }
    
    protected function getModalFeedbacks(Item $qtiItem){
        
        $returnValue = array();
        
        $feedbacks = $qtiItem->getModalFeedbacks();
        foreach($feedbacks as $feedback){
            $returnValue[$feedback->getSerial()] = $feedback->toArray();
        }
        
        return $returnValue;
    }
    
    public function getTemplateElements(Item $qtiItem){

        throw new common_Exception('qti template elments, to be implemented');
        //1. process templateRules
        //2. return the template values
    }

}
