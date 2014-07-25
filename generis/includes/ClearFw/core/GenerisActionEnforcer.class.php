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
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * ActionEnforcer class
 * TODO ActionEnforcer class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class GenerisActionEnforcer extends ActionEnforcer
{

	public function execute()
	{
		// get the extension of the action
		$extension	= common_ext_ExtensionsManager::singleton()->getExtensionById($this->context->getExtensionName());
		
		// get the module of the action
		$moduleName = $this->context->getModuleName() ? Camelizer::firstToUpper($this->context->getModuleName()) : DEFAULT_MODULE_NAME;
		$module		= $extension->getModule($moduleName);
		if (is_null($module)) {
			throw new ActionEnforcingException('Module "'.$moduleName.'" could not be loaded.',
											   	   $this->context->getModuleName(),
											       $this->context->getActionName());
		}
		
    	// get the action
		$action = $this->context->getActionName() ? Camelizer::firstToLower($this->context->getActionName()) : DEFAULT_ACTION_NAME;
    	
    	// if the method related to the specified action exists, call it
    	if (method_exists($module, $action)) {
    		
			$this->context->setActionName($action);
    		// search parameters method
    		$reflect	= new ReflectionMethod($module, $action);
    		$parameters	= $reflect->getParameters();

    		$tabParam 	= array();
    		foreach($parameters as $param)
    			$tabParam[$param->getName()] = $this->context->getRequest()->getParameter($param->getName());

    		// Action method is invoked, passing request parameters as
    		// method parameters.
    		common_Logger::d('Invoking '.get_class($module).'::'.$action, ARRAY('GENERIS', 'CLEARRFW'));
    		call_user_func_array(array($module, $action), $tabParam);
    		
    		// Render the view if selected.
    		if ($module->hasView())
    		{
    			$renderer = $module->getRenderer();
    			echo $renderer->render();
    		}
    	} 
    	else {
    		throw new ActionEnforcingException("Unable to find the action '".$action."' in '".get_class($module)."'.",
											   $this->context->getModuleName(),
											   $this->context->getActionName());
    	}
	}
}
?>