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
 * Short description of class tao_actions_form_Clazz
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_actions_form_Clazz
    extends tao_actions_form_Generis
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

		(isset($this->options['property_mode'])) ? $propMode = $this->options['property_mode'] : $propMode = 'simple';

		//add property action in toolbar
		$actions = tao_helpers_form_FormFactory::getCommonActions();
		$propertyElt = tao_helpers_form_FormFactory::getElement('property', 'Free');
		$propertyElt->setValue("<a href='#' class='property-adder'><img src='".TAOBASE_WWW."/img/prop_add.png'  /> ".__('Add property')."</a>");
		$actions[] = $propertyElt;

		//property mode
		$propModeELt = tao_helpers_form_FormFactory::getElement('propMode', 'Free');
		if($propMode == 'advanced'){
			$propModeELt->setValue("<a href='#' class='property-mode property-mode-simple' ><img src='".TAOBASE_WWW."/img/table_refresh.png'  /> ".__('Simple Mode')."</a>");
		}
		else{
			$propModeELt->setValue("<a href='#' class='property-mode property-mode-advanced' ><img src='".TAOBASE_WWW."/img/table_refresh.png'  /> ".__('Advanced Mode')."</a>");
		}
		$actions[] = $propModeELt;

		//add a hidden field that states it is a class edition form.
		$classElt = tao_helpers_form_FormFactory::getElement('tao.forms.class', 'Hidden');
		$classElt->setValue('1');
		$this->form->addElement($classElt);
		
		$this->form->setActions($actions, 'top');
 		$this->form->setActions($actions, 'bottom');

        
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
        

    	$clazz = $this->getClazz();

    	(isset($this->options['property_mode'])) ? $propMode = $this->options['property_mode'] : $propMode = 'simple';

    	//add a group form for the class edition
		$elementNames = array();
		foreach(tao_helpers_form_GenerisFormFactory::getDefaultProperties()  as $property){

			//map properties widgets to form elments
			$element = tao_helpers_form_GenerisFormFactory::elementMap($property);
			if(!is_null($element)){

				//take property values to populate the form
                $values = $clazz->getPropertyValues($property);
                if(!$property->isMultiple()){
                    if(count($values)>1){
                        $values = array_slice($values, 0, 1);
                    }
                }
				foreach($values as $value){
					if(!is_null($value)){
					    $element->setValue($value);
					}
				}
				$element->setName('class_'.$element->getName());
				$this->form->addElement($element);

				//set label validator
				if($property->getUri() == RDFS_LABEL){
					$element->addValidators(array(
						tao_helpers_form_FormFactory::getValidator('NotEmpty'),
					));
				}

				$elementNames[] = $element->getName();
			}
		}
		if(count($elementNames) > 0){
			$groupTitle = "<img src='".TAOBASE_WWW."img/class.png' /> ".__('Class').": "._dh($clazz->getLabel());
			$this->form->createGroup('class', $groupTitle, $elementNames);
		}

		//add an hidden elt for the class uri
		$classUriElt = tao_helpers_form_FormFactory::getElement('classUri', 'Hidden');
		$classUriElt->setValue(tao_helpers_Uri::encode($clazz->getUri()));
		$this->form->addElement($classUriElt);

		$localNamespace = common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();


		//class properties edition: add a group form for each property

		$classProperties = tao_helpers_form_GenerisFormFactory::getClassProperties($clazz, $this->getTopClazz());

		$i = 0;
		foreach($classProperties as $classProperty){
			$i++;
			$useEditor = false;
			$parentProp = true;
			$domains = $classProperty->getDomain();
			foreach($domains->getIterator() as $domain){
				if($domain->getUri() == $clazz->getUri()){
					$parentProp = false;

					//@todo use the getPrivileges method once implemented
					if(preg_match("/^".preg_quote($localNamespace, '/')."/", $classProperty->getUri())){
						$useEditor = true;
					}
					break;
				}
			}

			if($useEditor){

				//instanciate a property form

				$propFormClass = 'tao_actions_form_'.ucfirst(strtolower($propMode)).'Property';
				if(!class_exists($propFormClass)){
					$propFormClass = 'tao_actions_form_SimpleProperty';
				}

				$propFormContainer = new $propFormClass($clazz, $classProperty, array('index' => $i));
				$propForm = $propFormContainer->getForm();

				//and get its elements and groups
				$this->form->setElements(array_merge($this->form->getElements(), $propForm->getElements()));
				$this->form->setGroups(array_merge($this->form->getGroups(), $propForm->getGroups()));

				unset($propForm);
				unset($propFormContainer);
			}
			else if($parentProp){
				$domainElement = tao_helpers_form_FormFactory::getElement('parentProperty'.$i, 'Free');
				$value = __("Edit property into parent class ");
				foreach($domains->getIterator() as $domain){
					$value .= "<a  href='#' onclick='GenerisTreeBrowserClass.selectTreeNode(\"".tao_helpers_Uri::encode($domain->getUri())."\");' >".$domain->getLabel()."</a> ";
				}
				$domainElement->setValue($value);
				$this->form->addElement($domainElement);

				$groupTitle = "<img src='".TAOBASE_WWW."img/prop_orange.png' /> ".__('Property')." #".($i).": "._dh($classProperty->getLabel());
				$this->form->createGroup("parent_property_{$i}", $groupTitle, array('parentProperty'.$i));
			}
			else{
				$roElement = tao_helpers_form_FormFactory::getElement('roProperty'.$i, 'Free');
				$roElement->setValue(__("You cannot modify this property"));
				$this->form->addElement($roElement);

				$groupTitle = "<img src='".TAOBASE_WWW."img/prop_red.png' /> ".__('Property')." #".($i).": "._dh($classProperty->getLabel());
				$this->form->createGroup("ro_property_{$i}", $groupTitle, array('roProperty'.$i));
			}

		}

        
    }

} /* end of class tao_actions_form_Clazz */

?>