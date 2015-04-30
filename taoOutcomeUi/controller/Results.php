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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV); *
 */

namespace oat\taoOutcomeUi\controller;

use \Exception;
use \common_exception_IsAjaxAction;
use \core_kernel_classes_Resource;
use oat\tao\model\accessControl\AclProxy;
use \tao_actions_SaSModule;
use \tao_helpers_Request;
use \tao_helpers_Uri;
use oat\taoOutcomeUi\model\ResultsService;
use oat\taoOutcomeUi\helper\ResultLabel;

/**
 * Results Controller provide actions performed from url resolution
 *
 *
 * @author Patrick Plichart <patrick@taotesting.com>
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoOutcomeUi
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class Results extends tao_actions_SaSModule
{

    /**
     * constructor: initialize the service and the default data
     * @return Results
     */
    public function __construct()
    {
        parent::__construct();

        $this->defaultData();
    }

    protected function getClassService()
    {
        return ResultsService::singleton();
    }

    /**
     * Get all delivery execution to feed the tree
     * @throws \common_exception_IsAjaxAction
     */
    public function getOntologyData()
    {
        if (!tao_helpers_Request::isAjax()) {
            throw new common_exception_IsAjaxAction(__FUNCTION__);
        }
        
        $instances = array();
        $deliveryService = \taoDelivery_models_classes_DeliveryAssemblyService::singleton();
        if (!$this->hasRequestParameter('classUri') || $deliveryService->getRootClass()->getUri() === $this->getRequestParameter('classUri')) {
            // root
            foreach ($deliveryService->getAllAssemblies() as $assembly) {
                $child["attributes"] = array(
                    "id" => tao_helpers_Uri::encode($assembly->getUri()),
                    "class" => "node-class",
                    'data-uri' => $assembly->getUri()
                );
                $child["data"] = htmlentities($assembly->getLabel());
                $child["type"] = "class";

                $instances[] = $child;
            }
        }
        
        if(empty($instances) && !$this->hasRequestParameter('classUri')){
            $instances["attributes"] = array(
                "id" => $deliveryService->getRootClass()->getUri(),
                "class" => "node-class",
            );
            $instances["data"] = __('No Results');
        }

        $this->returnJson($instances);

    }


    /**
     * Action called on click on a delivery (class) construct and call the view to see the table of
     * all delivery execution for a specific delivery
     */
    public function index()
    {
        //Properties to filter on
        $properties = array(
            new \core_kernel_classes_Property(RDFS_LABEL),
        );

        $deliveryService = \taoDelivery_models_classes_DeliveryAssemblyService::singleton();
        $delivery = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
        if($delivery->getUri() !== $deliveryService->getRootClass()->getUri()){

            try{
                // display delivery
                $implementation = $this->getClassService()->getReadableImplementation($delivery);

                $this->getClassService()->setImplementation($implementation);

                $model = array();
                foreach($properties as $property){
                    $model[] = array(
                        'id'       => $property->getUri(),
                        'label'    => $property->getLabel(),
                        'sortable' => true
                    );
                }

                $this->setData('classUri',tao_helpers_Uri::encode($delivery->getUri()));
                $this->setData('model',$model);

                $this->setView('resultList.tpl');
            }
            catch(\common_exception_Error $e){
                $this->setData('type', 'error');
                $this->setData('error', $e->getMessage());
                $this->setView('index.tpl');
            }

        }
        else{
            $this->setData('type', 'info');
            $this->setData('error',__('No tests have been taken yet. As soon as a test-taker will take a test his results will be displayed here.'));
            $this->setView('index.tpl');
        }
    }


    /**
     * get all result delivery execution to display
     */
    public function getResults()
    {
        $page = $this->getRequestParameter('page');
        $limit = $this->getRequestParameter('rows');
        $order = $this->getRequestParameter('sortby');
        $sord = $this->getRequestParameter('sortorder');
        $start = $limit * $page - $limit;

        $gau = array(
            'order' 	=> $order,
            'orderdir'	=> strtoupper($sord),
            'offset'    => $start,
            'limit'		=> $limit,
            'recursive' => true
        );

        $delivery = new \core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));

        try{

        $implementation = $this->getClassService()->getReadableImplementation($delivery);

        $this->getClassService()->setImplementation($implementation);

        $data = array();
        $readOnly = array();
        $user = \common_session_SessionManager::getSession()->getUser();
        $rights = array(
            'view'=>!AclProxy::hasAccess($user, 'oat\taoOutcomeUi\controller\Results', 'viewResult',array()),
            'delete'=>!AclProxy::hasAccess($user, 'oat\taoOutcomeUi\controller\Results', 'delete',array()));
        $results = $this->getClassService()->getImplementation()->getResultByDelivery(array($delivery->getUri()), $gau);
        $counti = $this->getClassService()->getImplementation()->countResultByDelivery(array($delivery->getUri()));
        foreach($results as $res){

            $deliveryResult = new core_kernel_classes_Resource($res['deliveryResultIdentifier']);
            $testTaker = new core_kernel_classes_Resource($res['testTakerIdentifier']);
            $label = new ResultLabel($deliveryResult, $testTaker, $delivery);

            $data[] = array(
                'id'                           => $deliveryResult->getUri(),
                RDFS_LABEL                     => (string)$label,
            );

            $readOnly[$deliveryResult->getUri()] = $rights;
        }

        $this->returnJSON(array(
                'data' => $data,
                'page' => floor($start / $limit) + 1,
                'total' => ceil($counti / $limit),
                'records' => count($data),
                'readonly' => $readOnly
            ));
        }
        catch(\common_exception_Error $e){
            $this->returnJSON(array(
                    'error' => $e->getMessage()
                ));
        }
    }

    /**
     * Delete a result or a result class
     * @throws Exception
     * @return string json {'deleted' : true}
     */
    public function delete()
    {
        if (!tao_helpers_Request::isAjax()) {
            throw new Exception("wrong request mode");
        }
        $deliveryExecutionUri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
        $de = \taoDelivery_models_classes_execution_ServiceProxy::singleton()->getDeliveryExecution($deliveryExecutionUri);

        try{
            $implementation = $this->getClassService()->getReadableImplementation($de->getDelivery());
            $this->getClassService()->setImplementation($implementation);

            $deleted = $this->getClassService()->deleteResult($deliveryExecutionUri);

            $this->returnJson(array('deleted' => $deleted));
        }
        catch(\common_exception_Error $e){
            $this->returnJson(array('error' => $e->getMessage()));
        }
    }

    /**
     * Get info on the current Result and display it
     */
    public function viewResult()
    {
        $result = $this->getCurrentInstance();
        $de = \taoDelivery_models_classes_execution_ServiceProxy::singleton()->getDeliveryExecution($result->getUri());

        try{
            $implementation = $this->getClassService()->getReadableImplementation($de->getDelivery());
            $this->getClassService()->setImplementation($implementation);


            $testTaker = $this->getClassService()->getTestTakerData($result);

            if (
                (is_object($testTaker) and (get_class($testTaker) == 'core_kernel_classes_Literal'))
                or
                (is_null($testTaker))
            ) {
                //the test taker is unknown
                $this->setData('userLogin', $testTaker);
                $this->setData('userLabel', $testTaker);
                $this->setData('userFirstName', $testTaker);
                $this->setData('userLastName', $testTaker);
                $this->setData('userEmail', $testTaker);
            } else {
                $login = (count($testTaker[PROPERTY_USER_LOGIN]) > 0) ? current(
                    $testTaker[PROPERTY_USER_LOGIN]
                )->literal : "";
                $label = (count($testTaker[RDFS_LABEL]) > 0) ? current($testTaker[RDFS_LABEL])->literal : "";
                $firstName = (count($testTaker[PROPERTY_USER_FIRSTNAME]) > 0) ? current(
                    $testTaker[PROPERTY_USER_FIRSTNAME]
                )->literal : "";
                $userLastName = (count($testTaker[PROPERTY_USER_LASTNAME]) > 0) ? current(
                    $testTaker[PROPERTY_USER_LASTNAME]
                )->literal : "";
                $userEmail = (count($testTaker[PROPERTY_USER_MAIL]) > 0) ? current(
                    $testTaker[PROPERTY_USER_MAIL]
                )->literal : "";

                $this->setData('userLogin', $login);
                $this->setData('userLabel', $label);
                $this->setData('userFirstName', $firstName);
                $this->setData('userLastName', $userLastName);
                $this->setData('userEmail', $userEmail);
            }
            $filter = ($this->hasRequestParameter("filter")) ? $this->getRequestParameter("filter") : "lastSubmitted";
            $stats = $this->getClassService()->getItemVariableDataStatsFromDeliveryResult($result, $filter);
            $this->setData('nbResponses', $stats["nbResponses"]);
            $this->setData('nbCorrectResponses', $stats["nbCorrectResponses"]);
            $this->setData('nbIncorrectResponses', $stats["nbIncorrectResponses"]);
            $this->setData('nbUnscoredResponses', $stats["nbUnscoredResponses"]);
            $this->setData('deliveryResultLabel', $result->getLabel());
            $this->setData('variables', $stats["data"]);
            //retireve variables not related to item executions
            $deliveryVariables = $this->getClassService()->getVariableDataFromDeliveryResult($result);
            $this->setData('deliveryVariables', $deliveryVariables);
            $this->setData('uri', $this->getRequestParameter("uri"));
            $this->setData('classUri', $this->getRequestParameter("classUri"));
            $this->setData('filter', $filter);
            $this->setView('viewResult.tpl');
        }
        catch(\common_exception_Error $e){
            $this->setData('type', 'error');
            $this->setData('error', $e->getMessage());
            $this->setView('index.tpl');
            return;
        }
    }

    /**
     * Get the data for the file in the response and allow user to download it
     */
    public function getFile()
    {

        $variableUri = $this->getRequestParameter("variableUri");


        $delivery = new \core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('deliveryUri')));
        \common_Logger::w('delivery : '.print_r($delivery,true));
        try{
            $implementation = $this->getClassService()->getReadableImplementation($delivery);
            $this->getClassService()->setImplementation($implementation);


            $file = $this->getClassService()->getVariableFile($variableUri);
            $trace = $file["data"];
            header(
                'Set-Cookie: fileDownload=true'
            ); //used by jquery file download to find out the download has been triggered ...
            setcookie("fileDownload", "true", 0, "/");
            header("Content-type: " . $file["mimetype"]);
            if (!isset($file["filename"]) || $file["filename"] == "") {
                header('Content-Disposition: attachment; filename=download');
            } else {
                header('Content-Disposition: attachment; filename=' . $file["filename"]);
            }

            echo $file["data"];
        }
        catch(\common_exception_Error $e){
            echo $e->getMessage();
        }
    }
}
