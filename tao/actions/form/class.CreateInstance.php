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

use oat\tao\helpers\Template;

/**
 * Short description of class tao_actions_form_CreateInstance
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_actions_form_CreateInstance
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute classes
     *
     * @access private
     * @var array
     */
    private $classes = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array classes
     * @param  array options
     * @return mixed
     */
    public function __construct($classes, $options)
    {
        
        $this->classes = $classes;
    	parent::__construct(array(), $options);
        
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        
        $name = isset($this->options['name']) ? $this->options['name'] : 'form_'.(count(self::$forms)+1); 
		unset($this->options['name']);
		
        $this->form = tao_helpers_form_FormFactory::getForm($name, $this->options);
    	
		//add create action in toolbar
		$action = tao_helpers_form_FormFactory::getElement('save', 'Free');
		$value =  '<a href="#" class="form-submitter btn-success small"><span class="icon-save"></span> ' .__('Create').'</a>';

		$action->setValue($value);
		
		$this->form->setActions(array($action), 'top');
		$this->form->setActions(array($action), 'bottom');
        
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        
        $guiOrderProperty = new core_kernel_classes_Property(TAO_GUIORDER_PROP);
    	
    	//get the list of properties to set in the form
    	$defaultProperties 	= tao_helpers_form_GenerisFormFactory::getDefaultProperties();
		$editedProperties = $defaultProperties;
		$excludedProperties = (isset($this->options['excludedProperties']) && is_array($this->options['excludedProperties']))?$this->options['excludedProperties']:array();
		$additionalProperties = (isset($this->options['additionalProperties']) && is_array($this->options['additionalProperties']))?$this->options['additionalProperties']:array();
		$finalElements = array();
    	
		$classProperties = array();
		foreach ($this->classes as $class) {
			$classProperties = array_merge(tao_helpers_form_GenerisFormFactory::getClassProperties($class));
		}
		if(!empty($additionalProperties)){
			$classProperties = array_merge($classProperties, $additionalProperties);
		}
		
		foreach($classProperties as $property){
			if(!isset($editedProperties[$property->getUri()]) && !in_array($property->getUri(), $excludedProperties)){
				$editedProperties[$property->getUri()] = $property;
			}
		}
			
		foreach($editedProperties as $property){

			$property->feed();
			$widget = $property->getWidget();
			if($widget == null || $widget instanceof core_kernel_classes_Literal) {
				continue;
			}
			else if ($widget instanceof core_kernel_classes_Resource &&	$widget->getUri() == WIDGET_TREEVIEW){
			    continue;
			}
			
			//map properties widgets to form elments 
			$element = tao_helpers_form_GenerisFormFactory::elementMap($property);
			
			if(!is_null($element)){
				
				//set label validator
				if($property->getUri() == RDFS_LABEL){
					$element->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
				}

				// don't show empty labels
				if($element instanceof tao_helpers_form_elements_Label && strlen($element->getRawValue()) == 0) {
					continue;
				}
				
				//set file element validator:
				if($element instanceof tao_helpers_form_elements_AsyncFile){
					
				}
				
				if ($property->getUri() == RDFS_LABEL){
					// Label will not be a TAO Property. However, it should
					// be always first.
					array_splice($finalElements, 0, 0, array(array($element, 1)));
				}
				else if (count($guiOrderPropertyValues = $property->getPropertyValues($guiOrderProperty))){
					
					// get position of this property if it has one.
					$position = intval($guiOrderPropertyValues[0]);
					
					// insert the element at the right place.
					$i = 0;
					while ($i < count($finalElements) && ($position >= $finalElements[$i][1] && $finalElements[$i][1] !== null)){
						$i++;
					}
					
					array_splice($finalElements, $i, 0, array(array($element, $position)));
				}
				else{
					// Unordered properties will go at the end of the form.
					$finalElements[] = array($element, null);
				}
			}
		}
		
		// Add elements related to class properties to the form.
		foreach ($finalElements as $element){
			$this->form->addElement($element[0]);
		}
		
		// @todo currently tao cannot handle multiple classes
		/*
		$classUriElt = tao_helpers_form_FormFactory::getElement('classes', 'Hidden');
		$uris = array();
		foreach ($this->classes as $class) {
			$uris[] = $class->getUri();
		}
		$classUriElt->setValue($uris);
		*/
		
		//add an hidden elt for the class uri
		$classUriElt = tao_helpers_form_FormFactory::getElement('classUri', 'Hidden');
		$classUriElt->setValue(tao_helpers_Uri::encode($class->getUri()));
		$this->form->addElement($classUriElt);
		
		$this->form->addElement($classUriElt);
        
    }

}