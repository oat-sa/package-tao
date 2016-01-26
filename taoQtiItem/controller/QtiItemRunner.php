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

use oat\taoQtiItem\helpers\QtiFile;
use oat\taoQtiItem\controller\AbstractQtiItemRunner;
use \core_kernel_file_File;
use \core_kernel_classes_Resource;
use \common_Exception;
use \taoQtiCommon_helpers_PciVariableFiller;
use \taoQtiCommon_helpers_PciStateOutput;
use \taoQtiCommon_helpers_Utils;
use \common_Logger;
use \taoQtiCommon_helpers_ResultTransmissionException;
use \taoQtiCommon_helpers_ResultTransmitter;
use \taoResultServer_models_classes_ResultServerStateFull;
use qtism\runtime\common\State;
use qtism\runtime\tests\SessionManager;
use qtism\runtime\tests\AssessmentItemSession;
use qtism\runtime\tests\AssessmentItemSessionException;
use qtism\data\storage\StorageException;
use qtism\data\storage\xml\XmlDocument;

/**
 * Qti Item Runner Controller
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoQTI

 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class QtiItemRunner extends AbstractQtiItemRunner
{

    /**
     * The implementation of this method calls ItemRunner::setView in order to
     * select the view to be displayed.
     */
    protected function selectView() {
        $this->setInitialVariableElements();
        
        $this->setData('itemDataPath', $this->getRequestParameter('itemDataPath'));
        $this->setView('runtime/qti_item_runner.tpl', 'taoQtiItem');
    }

    /**
     * The endpoint specific to QTI Items
     * @return string
     */
    protected function getResultServerEndpoint() {
        return _url('', 'QtiItemRunner', 'taoQtiItem');
    }

    protected function getItemUri() {
        return $this->hasRequestParameter("itemId") ? $this->getRequestParameter("itemId") : '';
    }

    protected function getTestUri() {
        return $this->hasRequestParameter("testId") ? $this->getRequestParameter("testId") : '';
    }

    protected function getServiceCallId() {
        return $this->hasRequestParameter("serviceCallId") ? $this->getRequestParameter("serviceCallId") : '';
    }

    protected function getPostedTraces() {
        return $this->hasRequestParameter("traceVariables") ? $this->getRequestParameter("traceVariables") : array();
    }

    /**
     * The main entry poin for respon evaluation
     * 
     * @throws common_Exception
     */
    public function submitResponses() {

        $success = false;
        $itemUri = $this->getItemUri();

        if(!empty($itemUri)){

            $this->processResponses(new core_kernel_classes_Resource($itemUri));
            $success = true;
        }else{
            throw new common_Exception('missing required itemId');
        }
    }

    /**
     * Item's ResponseProcessing.
     * 
     * @param core_kernel_file_File $itemPath The Item file resource you want to apply ResponseProcessing.
     * @throws RuntimeException If an error occurs while processing responses or transmitting results
     */
    protected function processResponses(core_kernel_classes_Resource $item) {

        $jsonPayload = taoQtiCommon_helpers_Utils::readJsonPayload();

        try {
            $qtiXmlFilePath = QtiFile::getQtiFilePath($item);
            $qtiXmlDoc = new XmlDocument();
            $qtiXmlDoc->load($qtiXmlFilePath);
        }
        catch (StorageException $e) {
            $msg = "An error occured while loading QTI-XML file at expected location '${qtiXmlFilePath}'.";
            throw new RuntimeException($msg, 0, $e);
        }

        $itemSession = new AssessmentItemSession($qtiXmlDoc->getDocumentComponent(), new SessionManager());
        $itemSession->beginItemSession();

        $variables = array();

        // Convert client-side data as QtiSm Runtime Variables.
        foreach($jsonPayload as $identifier => $response) {
            
            $filler = new taoQtiCommon_helpers_PciVariableFiller($qtiXmlDoc->getDocumentComponent());
            
            try {
                $var = $filler->fill($identifier, $response);
                // Do not take into account QTI File placeholders.
                if (taoQtiCommon_helpers_Utils::isQtiFilePlaceHolder($var) === false) {
                    $variables[] = $var;
                }
            }
            catch(OutOfRangeException $e) {
                // A variable value could not be converted, ignore it.
                // Developer's note: QTI Pairs with a single identifier (missing second identifier of the pair) are transmitted as an array of length 1,
                // this might cause problem. Such "broken" pairs are simply ignored.
                common_Logger::d("Client-side value for variable '${identifier}' is ignored due to data malformation.");
            }
            catch(OutOfBoundsException $e) {
                // The response identifier does not match any response declaration.
                common_Logger::d("Uknown item variable declaration '${identifier}.");
            }
        }

        try {
            $itemSession->beginAttempt();
            $itemSession->endAttempt(new State($variables));

            // Transmit results to the Result Server.
            $this->transmitResults($item, $itemSession);

            // Return the item session state to the client-side.
            echo json_encode(array(
                'success' => true,
                'displayFeedback' => true,
                'itemSession' => self::buildOutcomeResponse($itemSession),
                'feedbacks' => $this->getFeedbacks($itemSession)
            ));
        }
        catch(AssessmentItemSessionException $e) {
            $msg = "An error occured while processing the responses.";
            throw new RuntimeException($msg, 0, $e);
        }
        catch(taoQtiCommon_helpers_ResultTransmissionException $e) {
            $msg = "An error occured while transmitting variable '${identifier}' to the target Result Server.";
            throw new RuntimeException($msg, 0, $e);
        }
    }

    /**
     * Transmit the variables contained in the AssessmentTestSession $itemSession as
     * item results to the Result Server.
     * 
     * @param core_kernel_classes_Resource $item The item definition in database.
     * @param AssessmentItemSession $itemSession The AssessmentItemSession objects from where the results must be extracted.
     * @throws taoQtiCommon_helpers_ResultTransmissionException If an error occurs while transmitting results to the ResultServer.
     */
    protected function transmitResults(core_kernel_classes_Resource $item, AssessmentItemSession $itemSession) { 
        $resultTransmitter = new taoQtiCommon_helpers_ResultTransmitter(taoResultServer_models_classes_ResultServerStateFull::singleton());

        foreach ($itemSession->getKeys() as $identifier) {
            // QTI built-in variables not suitable for this standalone QTI item execution case.
            if (!in_array($identifier, array('completionStatus', 'numAttempts', 'duration'))) {
                // Transmit to Result Server.
                $resultTransmitter->transmitItemVariable($itemSession->getVariable($identifier), $this->getServiceCallId(), $item->getUri());
            }
        }
    }

    protected static function buildOutcomeResponse(AssessmentItemSession $itemSession) {
        
        $stateOutput = new taoQtiCommon_helpers_PciStateOutput();

        foreach ($itemSession->getAllVariables() as $var) {
            $stateOutput->addVariable($var);
        }

        $output = $stateOutput->getOutput();
        return $output;
    }
}