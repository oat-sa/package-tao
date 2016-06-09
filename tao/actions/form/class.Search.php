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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Create a form to search the resources of the ontology
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_actions_form_Search
    extends tao_actions_form_Instance
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Initialize the form
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initForm()
    {
        
        
    	(isset($this->options['name'])) ? $name = $this->options['name'] : $name = ''; 
    	if(empty($name)){
			$name = 'form_'.(count(self::$forms)+1);
		}
		unset($this->options['name']);
			
		$this->form = tao_helpers_form_FormFactory::getForm($name, $this->options);
		
		//search action in toolbar
		$searchElt = tao_helpers_form_FormFactory::getElement('search', 'Free');
		$searchElt->setValue('<button type="button" class="form-submitter btn-success small"><span class="icon-find"></span>' .__('Search').'</button>');
		$this->form->setActions(array($searchElt), 'top');
		$this->form->setActions(array($searchElt), 'bottom');
    	
        
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return array
     */
    protected function getClassProperties(){
        return tao_helpers_form_GenerisFormFactory::getClassProperties($this->clazz, $this->getTopClazz());
        
    }

    /**
     * Initialize the form elements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initElements()
    {

        
    	$chainingElt = tao_helpers_form_FormFactory::getElement('chaining', 'Radiobox');
		$chainingElt->setDescription(__('Filtering mode'));
		$chainingElt->setOptions(array('or' =>  __('Exclusive (OR)'), 'and' => __('Inclusive (AND)')));
		$chainingElt->setValue('or');
		$this->form->addElement($chainingElt);
		
		$recursiveElt = tao_helpers_form_FormFactory::getElement('recursive', 'Checkbox');
		$recursiveElt->setDescription(__('Scope'));
		$recursiveElt->setOptions(array('0' =>  __('Search sub-classes')));
		$this->form->addElement($recursiveElt);

		$searchClassUriElt = tao_helpers_form_FormFactory::getElement("clazzUri", "Hidden");
		$searchClassUriElt->setValue(tao_helpers_Uri::encode($this->clazz->getUri()));
		$this->form->addElement($searchClassUriElt);
		
		$langElt = tao_helpers_form_FormFactory::getElement('lang', 'Combobox');
		$langElt->setDescription(__('Language'));
		
		$languages = array_merge(array('-- any --'), tao_helpers_I18n::getAvailableLangsByUsage(new core_kernel_classes_Resource(INSTANCE_LANGUAGE_USAGE_DATA)));
		$langElt->setOptions($languages);
		$langElt->setValue(0);
		$this->form->addElement($langElt);
		
		$this->form->createGroup('params', __('<del>Options</del>'), array('chaining', 'recursive', 'lang'));
		

		$filters = array();

		$defaultProperties 	= tao_helpers_form_GenerisFormFactory::getDefaultProperties();
		$classProperties	= $this->getClassProperties();

		
		$properties = array_merge($defaultProperties, $classProperties);
		
		(isset($this->options['recursive'])) ? $recursive = $this->options['recursive'] : $recursive = false;
		if($recursive){
			foreach($this->clazz->getSubClasses(true) as $subClass){
				$properties = array_merge($subClass->getProperties(false), $properties);
			}
		}
		
		foreach($properties as $property){
	
			$element = tao_helpers_form_GenerisFormFactory::elementMap($property);
			if( ! is_null($element) && 
				! $element instanceof tao_helpers_form_elements_Authoring && 
				! $element instanceof tao_helpers_form_elements_Hiddenbox &&
				! $element instanceof tao_helpers_form_elements_Hidden ){
				
				if($element instanceof tao_helpers_form_elements_MultipleElement){
					$newElement = tao_helpers_form_FormFactory::getElement($element->getName(), 'Checkbox');
					$newElement->setDescription($element->getDescription());
					$newElement->setOptions($element->getOptions());
					$element = $newElement;
				}
				if($element instanceof tao_helpers_form_elements_Htmlarea){
					$newElement = tao_helpers_form_FormFactory::getElement($element->getName(), 'Textarea');
					$newElement->setDescription($element->getDescription());
					$element = $newElement;
				}
				
				$this->form->addElement($element);
				$filters[] = $element->getName();
			}
		}
		$this->form->createGroup('filters', __('<del>Filters</del>'), $filters);
    	
        
    }

}