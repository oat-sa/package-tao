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

/**
 * Results Controller provide actions performed from url resolution
 *
 *
 * @author Patrick Plichart <patrick@taotesting.com>
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoResults
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoResults_actions_Results extends tao_actions_SaSModule {

	/**
	 * constructor: initialize the service and the default data
	 * @return Results
	 */
	public function __construct()
	{
		parent::__construct();
		
		//the service is initialized by default
		$this->service = taoResults_models_classes_ResultsService::singleton();
		
		
		$this->defaultData();
	}
	
	protected function getClassService()
	{
		return taoResults_models_classes_ResultsService::singleton();
	}
	
/*
 * controller actions
 */
	
	/**
	 * Edit a result class
	 * @return void
	 */
	public function editResultClass()
	{
		$clazz = $this->getCurrentClass();
		
		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getRootClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->getUri()));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit result class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', 'tao');
	}
	
	/**
	 * Delete a result or a result class
	 * @return void
	 */
	public function delete()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteResult($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteResultClass($this->getCurrentClass());
		}
		echo json_encode(array('deleted'	=> $deleted));
	}
	/**
     *
     * @author Patrick Plichart <patrick@taotesting.com>
     */
    public function viewResult()
    {   
        $result = $this->getCurrentInstance();

        $testTaker = $this->service->getTestTakerData($result);

        if (
                (is_object($testTaker) and (get_class($testTaker)=='core_kernel_classes_Literal'))
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
           $login = (count($testTaker[PROPERTY_USER_LOGIN])>0) ? current($testTaker[PROPERTY_USER_LOGIN])->literal :"";
            $label = (count($testTaker[RDFS_LABEL])>0) ? current($testTaker[RDFS_LABEL])->literal:"";
            $firstName = (count($testTaker[PROPERTY_USER_FIRSTNAME])>0) ? current($testTaker[PROPERTY_USER_FIRSTNAME])->literal:"";
            $userLastName = (count($testTaker[PROPERTY_USER_LASTNAME])>0) ? current($testTaker[PROPERTY_USER_LASTNAME])->literal:"";
            $userEmail = (count($testTaker[PROPERTY_USER_MAIL])>0) ? current($testTaker[PROPERTY_USER_MAIL])->literal:"";

            $this->setData('userLogin', $login);
            $this->setData('userLabel', $label);
            $this->setData('userFirstName', $firstName);
            $this->setData('userLastName', $userLastName);
            $this->setData('userEmail', $userEmail);
        }
        $filter = ($this->hasRequestParameter("filter")) ? $this->getRequestParameter("filter") : "lastSubmitted";
        $stats = $this->service->getItemVariableDataStatsFromDeliveryResult($result, $filter);
        $this->setData('nbResponses',  $stats["nbResponses"]);
        $this->setData('nbCorrectResponses',  $stats["nbCorrectResponses"]);
        $this->setData('nbIncorrectResponses',  $stats["nbIncorrectResponses"]);
        $this->setData('nbUnscoredResponses',  $stats["nbUnscoredResponses"]);   
        $this->setData('deliveryResultLabel', $result->getLabel());
        $this->setData('variables',  $stats["data"]);
        //retireve variables not related to item executions
        $deliveryVariables = $this->service->getVariableDataFromDeliveryResult($result);
        $this->setData('deliveryVariables', $deliveryVariables);
        $this->setData('uri',$this->getRequestParameter("uri"));
        $this->setData('classUri',$this->getRequestParameter("classUri"));
        $this->setData('filter',$filter);
        $this->setView('viewResult.tpl');
    }
   
     public function getFile(){
        $variableUri = $this->getRequestParameter("variableUri");
        $file = $this->service->getVariableFile($variableUri); 
        $trace = $file["data"];
        header('Set-Cookie: fileDownload=true'); //used by jquery file download to find out the download has been triggered ...
        setcookie("fileDownload","true", 0, "/");
        header("Content-type: ".$file["mimetype"]);
        if (!isset($file["filename"]) || $file["filename"]==""){
            header('Content-Disposition: attachment; filename=download');
        } else {
            header('Content-Disposition: attachment; filename='.$file["filename"]);
        }
        
        echo $file["data"];
    }
}
?>