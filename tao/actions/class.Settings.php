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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * This controller provide the actions to manage classes optimizations.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage actions
 *
 */
class tao_actions_Settings extends tao_actions_CommonModule {

	/**
	 * Create a new instance of tao_actions_Settings.
	 * 
	 * @access public
	 */
	public function __construct(){
		parent::__construct();
	}


	
	/* (non-PHPdoc)
	 * @see tao_actions_CommonModule::_isAllowed()
	 */
	protected function _isAllowed() {
		return parent::_isAllowed() && tao_helpers_SysAdmin::isSysAdmin();
	}

	
	/**
	 * This action displays the classes that are optimizable. The classes
	 * that are considered to be optimizable are discovered by using the
	 * tao_helpers_Optimization helper class.
	 * 
	 * Its related view is /tao/forms/settings_optimize.tpl.
	 * It sets the 'optimizable' data variable to the view, containing
	 * the classes that are considered to be optimizable.
	 * 
	 * @see tao_helpers_Optimization::getOptimizableClasses() For the format of the 'optimizable' data key.
	 */
	public function index(){

		$optimizableClasses = tao_helpers_Optimization::getOptimizableClasses();

		if(!empty($optimizableClasses)){
			$this->setData('optimizable', true);
		}
		
		$this->defaultData();
		$this->setView('form/settings_optimize.tpl');

	}


    /**
     * Returns the classes that are optimizable as a JSON array. 
     * An example of such a structure:
     * 
     * [
     * 		{
     * 			"class": "User",
     * 			"classUri": "http://www.tao.lu/Ontologies/generis.rdf#User",
     * 			"status": "compiled",
     * 			"action": ""
     * 		},
     * 		...
     * 		...
     * ]
     */
    public function optimizeClasses(){
    	
		echo json_encode(tao_helpers_Optimization::getOptimizableClasses());
    }

    /**
     * Returns an array of Property URIs that are considered to be optimizabled (indexable).
     * 
     * @return array
     */
    protected function getOptimizableProperties(){

		$returnValue = array();

		$extManager = common_ext_ExtensionsManager::singleton();
		$extensions = $extManager->getInstalledExtensions();
		
		foreach ($extensions as $ext){
			$returnValue = array_merge($returnValue, $ext->getOptimizableProperties());
		}

		return $returnValue;
    }

    /**
     * This action aims at compiling a specific class given as the 'classUri' request parameter.
     * 
     * It returns a JSON structure containing the following informations:
     * 
     * {
     *    "success": true/false,
     *    "count": integer // the class instances that were optimized
     *    "relatedClasses": ["class1", "class2", ...] // the classes that were impacted by the optimization
     *                                                // depending on the optimization options 
     * }
     * 
     */
    public function compileClass(){

		$class = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
		$result = tao_helpers_Optimization::compileClass($class);

		echo json_encode($result);
    }

    /**
     * This action aims at unoptimize a specific class given as the 'classUri' request parameter.
     * 
     * It returns a JSON structure containing the following informations:
     * 
     * {
     *    "success": true/false,
     *    "count": integer // the class instances that were unoptimized
     *    "relatedClasses": ["class1", "class2", ...] // the classes that were impacted by the unoptimization
     *                                                // depending on the unoptimization options 
     * }
     */
    public function decompileClass(){

		$class = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
		$result = tao_helpers_Optimization::decompileClass($class);
		
		echo json_encode($result);
    }

    /**
     * This action aims at optimizing (indexing) the properties that are considered to be
     * optimizable by the system. The optimizable properties are retrieved from the manifests
     * of extensions that are installed on the platform.
     * 
     * It returns a JSON structure:
     * 
     * {
     *    "success": true/false // depending on the success or the failure of the action.
     * }
     */
    public function createPropertyIndex(){

		$properties = $this->getOptimizableProperties();
		$result = array(
			'success' => core_kernel_persistence_Switcher::createIndex($properties)
		);

		echo json_encode($result);
    }

}
?>