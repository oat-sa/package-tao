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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Author an QTI test
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoQtiTest
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoQtiTest_actions_Import extends tao_actions_CommonModule {

    public function __construct(){
        parent::__construct();
        $this->service = tao_models_classes_TaoService::singleton();
        $this->defaultData();
    }
    
    /**
     * Display a very basic import interface
     */
	public function index()
	{
	    if (!$this->hasRequestParameter('uri') || strlen($this->getRequestParameter('uri')) == 0) {
	        throw new common_exception_MissingParameter('uri', __CLASS__);
	    }
	    $test = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
	     
	    $formContainer = new taoQtiTest_models_forms_ImportForm($test);
	    $myForm = $formContainer->getForm();
	    
	    if($myForm->isSubmited()){
	        if($myForm->isValid()){
	            $fileInfo = $myForm->getValue('source');
	            $uploadedFile = $fileInfo['uploaded_file'];
	            
	            $itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
	            $subClass = $itemClass->createSubClass($test->getLabel());
	            $report = taoQtiTest_models_classes_QtiTestService::singleton()->importTest($test, $uploadedFile, $subClass);
	            if ($report->containsSuccess()) {
	                $this->setData('message', __('Content saved'));
	            }
	            if ($report->containsError()) {
	                $this->setData('importErrorTitle', $report->getTitle());
	                $this->setData('importErrors', $report->getErrors());
	            }
	        }
	    }
	    $this->setData('formTitle', __('Import Content'));
	    $this->setData('myForm', $myForm->render());
	    $this->setView('form.tpl');
	}
}
