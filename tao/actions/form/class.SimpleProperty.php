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
use oat\taoBackOffice\model\tree\TreeService;

/**
 * Enable you to edit a property
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_actions_form_SimpleProperty extends tao_actions_form_AbstractProperty
{

    /**
     * Initialize the form elements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     */
    protected function initElements()
    {
        
    	$property = new core_kernel_classes_Property($this->instance->getUri());

	    $index = $this->getIndex();

	    $propertyProperties = array_merge(
			tao_helpers_form_GenerisFormFactory::getDefaultProperties(), 
			array(new core_kernel_classes_Property(PROPERTY_IS_LG_DEPENDENT),
				  new core_kernel_classes_Property(TAO_GUIORDER_PROP))
		);
    	
    	$elementNames = array();
		foreach($propertyProperties as $propertyProperty){
		
			//map properties widgets to form elements
			$element = tao_helpers_form_GenerisFormFactory::elementMap($propertyProperty);
			
			if(!is_null($element)){
				//take property values to populate the form
				$values = $property->getPropertyValuesCollection($propertyProperty);
				foreach($values->getIterator() as $value){
					if(!is_null($value)){
						if($value instanceof core_kernel_classes_Resource){
							$element->setValue($value->getUri());
						}
						if($value instanceof core_kernel_classes_Literal){
							$element->setValue((string)$value);
						}
					}
				}
				$element->setName("{$index}_{$element->getName()}");
                $element->addClass('property');

                if ($propertyProperty->getUri() == TAO_GUIORDER_PROP){
                    $element->addValidator(tao_helpers_form_FormFactory::getValidator('Integer'));
                }
                if ($propertyProperty->getUri() == RDFS_LABEL){
                    $element->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
                }
				$this->form->addElement($element);
				$elementNames[] = $element->getName();
			}
		}
		
		//build the type list from the "widget/range to type" map
		$typeElt = tao_helpers_form_FormFactory::getElement("{$index}_type", 'Combobox');
		$typeElt->setDescription(__('Type'));
		$typeElt->addAttribute('class', 'property-type property');
		$typeElt->setEmptyOption(' --- '.__('select').' --- ');
		$options = array();
		$checkRange = false;
		foreach(tao_helpers_form_GenerisFormFactory::getPropertyMap() as $typeKey => $map){
			$options[$typeKey] = $map['title'];
            $widget = $property->getWidget();
			if($widget instanceof core_kernel_classes_Resource) {
				if($widget->getUri() == $map['widget']){
					$typeElt->setValue($typeKey);
					$checkRange = is_null($map['range']);
				}
			}
		}
		$typeElt->setOptions($options);
		$this->form->addElement($typeElt);
		$elementNames[] = $typeElt->getName();

	    $range = $property->getRange();

	    $rangeSelect = tao_helpers_form_FormFactory::getElement( "{$this->getIndex()}_range", 'Combobox' );
	    $rangeSelect->setDescription( __( 'List values' ) );
	    $rangeSelect->addAttribute( 'class', 'property-listvalues property' );
	    $rangeSelect->setEmptyOption( ' --- ' . __( 'select' ) . ' --- ' );

	    if ($checkRange) {
		    $rangeSelect->addValidator( tao_helpers_form_FormFactory::getValidator( 'NotEmpty' ) );
	    }

	    $this->form->addElement($rangeSelect);
	    $elementNames[] = $rangeSelect->getName();

	    //list drop down
	    $listElt = $this->getListElement( $range );
		$this->form->addElement($listElt);
		$elementNames[] = $listElt->getName();

	    //trees dropdown
	    $treeElt = $this->getTreeElement( $range );
	    $this->form->addElement($treeElt);
	    $elementNames[] = $treeElt->getName();

	    //index part
        $indexes = $property->getPropertyValues(new \core_kernel_classes_Property(INDEX_PROPERTY));
        foreach($indexes as $i => $indexUri){
            $indexProperty = new \oat\tao\model\search\Index($indexUri);
            $indexFormContainer = new tao_actions_form_IndexProperty($this->getClazz(), $indexProperty,
                array('property' => $property->getUri(),
                    'propertyindex' => $index,
                    'index' => $i)
            );
            /** @var tao_helpers_form_Form $indexForm */
            $indexForm = $indexFormContainer->getForm();
            foreach($indexForm->getElements() as $element){
                $this->form->addElement($element);
                $elementNames[] = $element->getName();
            }
        }

        //add this element only when the property is defined (type)
        if(!is_null($property->getRange())){
            $addIndexElt = tao_helpers_form_FormFactory::getElement("index_{$index}_add", 'Free');
            $addIndexElt->setValue(
                "<a href='#' class='btn-info index-adder small index'><span class='icon-add'></span> " . __(
                    'Add index'
                ) . "</a><div class='clearfix'></div>"
            );
            $this->form->addElement($addIndexElt);
            $elementNames[] = $addIndexElt;
        }
        else{
            $addIndexElt = tao_helpers_form_FormFactory::getElement("index_{$index}_p", 'Free');
            $addIndexElt->setValue(
                "<p class='index' >" . __(
                    'Choose a type for your property first'
                ) . "</p>"
            );
            $this->form->addElement($addIndexElt);
            $elementNames[] = $addIndexElt;
        }

        //add an hidden elt for the property uri
        $encodedUri = tao_helpers_Uri::encode($property->getUri());
        $propUriElt = tao_helpers_form_FormFactory::getElement("{$index}_uri", 'Hidden');
        $propUriElt->addAttribute('class', 'property-uri property');
        $propUriElt->setValue($encodedUri);
        $this->form->addElement($propUriElt);
        $elementNames[] = $propUriElt;

		if(count($elementNames) > 0){
			$groupTitle = $this->getGroupTitle($property);
			$this->form->createGroup("property_{$encodedUri}", $groupTitle, $elementNames);
		}

    }

	/**
	 * @param $range
	 *
	 * @return tao_helpers_form_elements_xhtml_Combobox
	 * @throws common_Exception
	 */
	protected function getTreeElement( $range )
	{

		$dataService = TreeService::singleton();
		/**
		 * @var tao_helpers_form_elements_xhtml_Combobox $element
		 */
		$element     = tao_helpers_form_FormFactory::getElement( "{$this->getIndex()}_range_tree", 'Combobox' );
		$element->setDescription( __( 'Tree values' ) );
		$element->addAttribute( 'class', 'property-template tree-template' );
		$element->addAttribute( 'disabled', 'disabled' );
		$element->setEmptyOption( ' --- ' . __( 'select' ) . ' --- ' );
		$treeOptions = array();
		foreach ($dataService->getTrees() as $tree) {
			$treeOptions[tao_helpers_Uri::encode( $tree->getUri() )] = $tree->getLabel();
			if (null !== $range && $range->getUri() === $tree->getUri()) {
				$element->setValue( $tree->getUri() );
			}
		}
		$element->setOptions( $treeOptions );

		return $element;
	}

	/**
	 * @param $range
	 *
	 * @return tao_helpers_form_elements_xhtml_Combobox
	 * @throws common_Exception
	 */
	protected function getListElement( $range )
	{

		$service = tao_models_classes_ListService::singleton();

		/**
		 * @var tao_helpers_form_elements_xhtml_Combobox $element
		 */
		$element = tao_helpers_form_FormFactory::getElement( "{$this->getIndex()}_range_list", 'Combobox' );
		$element->setDescription( __( 'List values' ) );
		$element->addAttribute( 'class', 'property-template list-template' );
		$element->addAttribute( 'disabled', 'disabled' );
		$element->setEmptyOption( ' --- ' . __( 'select' ) . ' --- ' );
		$listOptions = array();

		foreach ($service->getLists() as $list) {
			$listOptions[tao_helpers_Uri::encode( $list->getUri() )] = $list->getLabel();
			if (null !== $range && $range->getUri() === $list->getUri()) {
				$element->setValue( $list->getUri() );
			}
		}

		$listOptions['new'] = ' + ' . __( 'Add / Edit lists' );
		$element->setOptions( $listOptions );

		return $element;
	}


}