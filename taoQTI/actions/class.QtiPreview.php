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

use qtism\runtime\common\State;
use qtism\runtime\tests\AssessmentItemSession;
use qtism\runtime\tests\AssessmentItemSessionException;
use qtism\data\storage\xml\XmlAssessmentItemDocument;
use qtism\data\storage\StorageException;

/**
 * Qti Item Runner Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoQTI
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoQTI_actions_QtiPreview extends taoItems_actions_ItemPreview
{
    public function submitResponses(){
    
        $itemUri = tao_helpers_Uri::decode($this->getRequestParameter('itemUri'));
    
        if(!empty($itemUri)){
            $this->processResponses(new core_kernel_classes_Resource($itemUri), $this->getPostedResponses());
        }else{
            throw new common_Exception('missing required itemUri');
        }
    }
    
    protected function getPostedResponses(){
        return $this->hasRequestParameter("responseVariables") ? $this->getRequestParameter("responseVariables") : array();
    }
    
    /**
     * Item's ResponseProcessing.
     *
     * @param core_kernel_classes_Resource $item The Item you want to apply ResponseProcessing.
     * @param array $responses Client-side responses.
     * @throws RuntimeException If an error occurs while processing responses or transmitting results
     */
    protected function processResponses(core_kernel_classes_Resource $item, array $responses){
        $itemContentProperty = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
        $contentResource = $item->getUniquePropertyValue($itemContentProperty);
        $qtiXmlFile = new core_kernel_file_File($contentResource->getUri());
    
        try {
            $qtiXmlFilePath = $qtiXmlFile->getAbsolutePath();
            $qtiXmlDoc = new XmlAssessmentItemDocument();
            $qtiXmlDoc->load($qtiXmlFilePath);
        }
        catch (StorageException $e) {
            $msg = "An error occured while loading QTI-XML file at expected location '${qtiXmlFilePath}'.";
            throw new RuntimeException($msg, 0, $e);
        }
        
        $itemSession = new AssessmentItemSession($qtiXmlDoc);
        $itemSession->beginItemSession();
        
        $variables = array();
        
        // Convert client-side data as QtiSm Runtime Variables.
        foreach ($responses as $identifier => $response) {
            $filler = new taoQtiCommon_helpers_VariableFiller($qtiXmlDoc);
            try {
                $variables[] = $filler->fill($identifier, $response);
            }
            catch (OutOfRangeException $e) {
                // A variable value could not be converted, ignore it.
                // Developer's note: QTI Pairs with a single identifier (missing second identifier of the pair) are transmitted as an array of length 1,
                // this might cause problem. Such "broken" pairs are simply ignored.
                common_Logger::d("Client-side value for variable '${identifier}' is ignored due to data malformation.");
            }
        }
    
        try {
            $itemSession->beginAttempt();
            $itemSession->endAttempt(new State($variables));
    
            // Return the item session state to the client-side.
            echo json_encode(array('success' => true, 'displayFeedback' => true, 'itemSession' => self::buildOutcomeResponse($itemSession)));
        }
        catch (AssessmentItemSessionException $e) {
            $msg = "An error occured while processing the responses.";
            throw new RuntimeException($msg, 0, $e);
        }
        catch (taoQtiCommon_helpers_ResultTransmissionException $e) {
            $msg = "An error occured while transmitting variable '${identifier}' to the target Result Server.";
            throw new RuntimeException($msg, 0, $e);
        }
    }
    
    /**
     * Get the ResultServer API call to be used by the item.
     *
     * @return string A string representing JavaScript instructions.
     */
    protected function getResultServerApi() {
        $root = ROOT_URL;
        $itemUri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
        return "new QtiPreviewResultServerApi('${root}taoQTI/QtiPreview/', '${itemUri}')";
    }
    
    /**
     * Get the path from ROOT_URL where the ResultServerApi implementation is found on the server.
     *
     * @return string
     */
    protected function getResultServerApiPath() {
        return 'taoQTI/views/js/QtiPreviewResultServerApi.js';
    }
    
    protected static function buildOutcomeResponse(AssessmentItemSession $itemSession) {
        $stateOutput = new taoQtiCommon_helpers_StateOutput();
    
        foreach ($itemSession->getOutcomeVariables(false) as $var) {
            $stateOutput->addVariable($var);
        }
    
        $output = $stateOutput->getOutput();
        return $output;
    }
}