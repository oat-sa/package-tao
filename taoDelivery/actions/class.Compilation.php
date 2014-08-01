<?php
/*
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; under version 2 of the License (non-upgradable). This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2); 2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER); 2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 */

/**
 * Delivery Controller provide actions performed from url resolution
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 
 * @license GPLv2 http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_actions_Compilation extends tao_actions_SaSModule
{

    /**
     * constructor: initialize the service and the default data
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return Delivery
     */
    public function __construct()
    {
        parent::__construct();
        
        // the service is initialized by default
        $this->service = taoDelivery_models_classes_DeliveryAssemblyService::singleton();
        $this->defaultData();
    }

    /**
     * (non-PHPdoc)
     * @see tao_actions_SaSModule::getClassService()
     */
    protected function getClassService()
    {
        return $this->service;
    }
    
    /*
     * controller actions
    */
    /**
     * Render json data to populate the delivery tree
     * 'modelType' must be in the request parameters
     *
     * @return void
     */
    public function index()
    {
		$delivery = $this->getCurrentInstance();
		$this->setData('uri', $delivery->getUri());
		$this->setData('classUri', $this->getCurrentClass()->getUri());
		$this->setData("deliveryLabel", $delivery->getLabel());
		
		//compilation state:
		$compiled = $this->service->getAssembliesByTemplate($delivery, true);
		$this->setData("isCompiled", !empty($compiled));
		
		$this->setView("delivery_compiling.tpl");
    }
    
	public function compile(){
	    $delivery = $this->getCurrentInstance();
	    $report = $this->service->createAssemblyFromTemplate($delivery);
	    
	    $this->setData('title', __('Publishing Report'));
	    $this->setData('report', $report);
	    $this->setView('report.tpl', 'tao');
	}
	
	public function export() {
	    $publishedDelivery = $this->getCurrentInstance();
	    $path = taoDelivery_models_classes_import_Assembler::exportCompiledDelivery($publishedDelivery);
	    echo json_encode(array(
	        'success' => true,
	        'download'   => _url('downloadExportedFiles', 'Export', 'tao', array('filePath' => tao_helpers_Export::getRelativPath($path)))
	    ));
	    /*
	    echo json_encode(array(
	        'success' => false,
	        'error'   => $e instanceof common_exception_UserReadableException ? $e->getUserMessage() : __('An undefined error has occured')
	    ));
	    */
	}
}