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
 * The GenerisFormFactory enables you to create Forms using rdf data and the
 * api to provide it. You can give any node of your ontology and the factory
 * create the appriopriate form. The Generis ontology (with the Widget Property)
 * required to use it.
 * Now only the xhtml rendering mode is implemented
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @see core_kernel_classes_* packages
 
 */
class tao_helpers_form_GenerisFormFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Enable you to map an rdf property to a form element using the Widget
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Property property
     * @return tao_helpers_form_FormElement
     */
    public static function elementMap( core_kernel_classes_Property $property)
    {
        $returnValue = null;
		
		//create the element from the right widget
        $property->feed();
        
        $widgetResource = $property->getWidget();
		if (is_null($widgetResource)) {
			return null;
		}
		
		//authoring widget is not used in standalone mode
		if ($widgetResource->getUri() == 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Authoring'
		    && tao_helpers_Context::check('STANDALONE_MODE')) {
			return null;
		}
		
		// horrible hack to fix file widget
		if ($widgetResource->getUri() == 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#AsyncFile') {
		    $widgetResource = new core_kernel_classes_Resource('http://www.tao.lu/datatypes/WidgetDefinitions.rdf#GenerisAsyncFile');
		}
		
		$element = tao_helpers_form_FormFactory::getElementByWidget(tao_helpers_Uri::encode($property->getUri()), $widgetResource);
		
		if(!is_null($element)){
		    if($element->getWidget() != $widgetResource->getUri()){
                common_Logger::w('Widget definition differs from implementation: '.$element->getWidget().' != '.$widgetResource->getUri());
		        return null;
			}
				
			//use the property label as element description
			$propDesc = (strlen(trim($property->getLabel())) > 0) ? $property->getLabel() : str_replace(LOCAL_NAMESPACE, '', $property->getUri());
			$element->setDescription($propDesc);
			
			//multi elements use the property range as options
			if(method_exists($element, 'setOptions')){
				$range = $property->getRange();
				
				if($range != null){
					$options = array();
					
					if($element instanceof tao_helpers_form_elements_Treeview){
						if($property->getUri() == RDFS_RANGE){
							$options = self::rangeToTree(new core_kernel_classes_Class(RDFS_RESOURCE));
						}
						else{
							$options = self::rangeToTree($range);
						}
					}
					else{
						foreach($range->getInstances(true) as $rangeInstance){
							$options[ tao_helpers_Uri::encode($rangeInstance->getUri()) ] = $rangeInstance->getLabel();
						}
						//set the default value to an empty space
						if(method_exists($element, 'setEmptyOption')){
							$element->setEmptyOption(' ');
						}
					}
					
					//complete the options listing
					$element->setOptions($options);
				}
			}
			$returnValue = $element;
		}
		
        

        return $returnValue;
    }

    /**
     * Enable you to get the properties of a class. 
     * The advantage of this method is to limit the level of recusrivity in the
     * It get the properties up to the defined top class
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @param  Class topLevelClazz
     * @return array
     */
    public static function getClassProperties( core_kernel_classes_Class $clazz,  core_kernel_classes_Class $topLevelClazz = null)
    {
        $returnValue = array();

        
		
		 
        if(is_null($topLevelClazz)){
			$topLevelClazz = new core_kernel_classes_Class(TAO_OBJECT_CLASS);
		}
		
		
		if($clazz->getUri() == $topLevelClazz->getUri()){
			$returnValue = $clazz->getProperties(false);
			return (array) $returnValue;
		}
		
		//determine the parent path
		$parents = array();
		$top = false;
		do{
			if(!isset($lastLevelParents)){
				$parentClasses = $clazz->getParentClasses(false);
			}
			else{
				$parentClasses = array();
				foreach($lastLevelParents as $parent){
					$parentClasses = array_merge($parentClasses, $parent->getParentClasses(false));
				}
			}
			if(count($parentClasses) == 0){
				break;
			}
			$lastLevelParents = array();
			foreach($parentClasses as $parentClass){
				if($parentClass->getUri() == RDFS_CLASS){
					continue;
				}
				if($parentClass->getUri() == $topLevelClazz->getUri() ) {
					$parents[$parentClass->getUri()] = $parentClass;	
					$top = true;
					break;
				}
				
				
				$allParentClasses = $parentClass->getParentClasses(true);
				if(array_key_exists($topLevelClazz->getUri(), $allParentClasses)){
					 $parents[$parentClass->getUri()] = $parentClass;
				}
				
				$lastLevelParents[$parentClass->getUri()] = $parentClass;
			}
		}while(!$top);
		
		foreach($parents as $parent){
			$returnValue = array_merge($returnValue, $parent->getProperties(false));
    	}
    	$returnValue = array_merge($returnValue, $clazz->getProperties(false));
		
        

        return (array) $returnValue;
    }

    /**
     * get the default properties to add to every forms
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public static function getDefaultProperties()
    {
        $returnValue = array();

        

		 $returnValue = array(
			new core_kernel_classes_Property(RDFS_LABEL)
		);
		
        

        return (array) $returnValue;
    }

    /**
     * Get the properties of the rdfs Property class
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string mode
     * @return array
     */
    public static function getPropertyProperties($mode = 'simple')
    {
        $returnValue = array();

        
		
		switch($mode){
			case 'simple':
				$defaultUris = array(PROPERTY_IS_LG_DEPENDENT);
				break;
			case 'advanced':
			default:	
				$defaultUris = array(
					RDFS_LABEL,
					PROPERTY_WIDGET,
					RDFS_RANGE,
					PROPERTY_IS_LG_DEPENDENT
				);
				break;
		} 
		$resourceClass = new core_kernel_classes_Class(RDF_PROPERTY);
		foreach($resourceClass->getProperties() as $property){
			if(in_array($property->getUri(), $defaultUris)){
				array_push($returnValue, $property);
			}
		}
		
        

        return (array) $returnValue;
    }

    /**
     * Returnn the map between the Property properties: range, widget, etc. to
     * shorcuts for the simplePropertyEditor
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public static function getPropertyMap()
    {
        $returnValue = array();

        
		
		$returnValue = array(
			'text' => array(
				'title' 	=> __('Text - Short - Field'),
				'widget'	=> PROPERTY_WIDGET_TEXTBOX,
				'range'		=> RDFS_LITERAL,
			    'multiple'  => GENERIS_FALSE
			),
			'longtext' => array(
				'title' 	=> __('Text - Long - Box'),
				'widget'	=> PROPERTY_WIDGET_TEXTAREA,
				'range'		=> RDFS_LITERAL,
			    'multiple'  => GENERIS_FALSE
			),
			'html' => array(
				'title' 	=> __('Text - Long - HTML editor'),
				'widget'	=> PROPERTY_WIDGET_HTMLAREA,
				'range'		=> RDFS_LITERAL,
			    'multiple'  => GENERIS_FALSE
			),
			'list' => array(
				'title' 	=> __('List - Single choice - Radio button'),
				'widget'	=> PROPERTY_WIDGET_RADIOBOX,
				'range'		=> RDFS_RESOURCE,
			    'multiple'  => GENERIS_FALSE
			),
			'longlist' => array(
				'title' 	=> __('List - Single choice - Drop down'),
				'widget'	=> PROPERTY_WIDGET_COMBOBOX,
				'range'		=> RDFS_RESOURCE,
			    'multiple'  => GENERIS_FALSE
			),
			'multilist' => array(
				'title' 	=> __('List - Multiple choice - Check box'),
				'widget'	=> PROPERTY_WIDGET_CHECKBOX,
				'range'		=> RDFS_RESOURCE,
			    'multiple'  => GENERIS_TRUE
			),
			'calendar' => array(
				'title' 	=> __('Calendar'),
				'widget'	=> PROPERTY_WIDGET_CALENDAR,
				'range'		=> RDFS_LITERAL,
			    'multiple'  => GENERIS_FALSE
			),
			'password' => array(
				'title' 	=> __('Password'),
				'widget'	=> PROPERTY_WIDGET_HIDDENBOX,
				'range'		=> RDFS_LITERAL,
			    'multiple'  => GENERIS_FALSE
			),
			'file' => array(
				'title' 	=> __('File'),
				'widget'	=> PROPERTY_WIDGET_FILE,
				'range'		=> CLASS_GENERIS_FILE,
			    'multiple'  => GENERIS_FALSE
			)
		);
		
        

        return (array) $returnValue;
    }

    /**
     * Short description of method rangeToTree
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class range
     * @param  boolean recursive
     * @return array
     */
    protected static function rangeToTree( core_kernel_classes_Class $range, $recursive = false)
    {
        $returnValue = array();

        
        
        
        $data = array();
    	foreach($range->getSubClasses(false) as $rangeClass){
			$classData = array(
				'data' => $rangeClass->getLabel(),
				'attributes' => array(
					'id' => tao_helpers_Uri::encode($rangeClass->getUri()), 
					'class' => 'node-instance'
				)
			);
			$children = self::rangeToTree($rangeClass, true);
			if(count($children) > 0){
				$classData['state'] = 'closed';
				$classData['children'] = $children;
			}
			
			$data[] = $classData;
		}
		if(!$recursive){
        	$returnValue = array(
				'data' => $range->getLabel(),
				'attributes' => array(
					'id' => tao_helpers_Uri::encode($range->getUri()), 
					'class' => 'node-root'
				),
				'children' => $data
			);
        }
        else{
        	$returnValue = $data;
        }
        
        

        return (array) $returnValue;
    }

    /**
     * Short description of method extractTreeData
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array data
     * @param  boolean recursive
     * @return array
     */
    public static function extractTreeData($data, $recursive = false)
    {
        $returnValue = array();

        
        
        
        if(isset($data['data'])){
        	$data = array($data);
        }
        foreach($data as $node){
        	$returnValue[$node['attributes']['id']] = $node['data'];
        	if(isset($node['children'])){
        		$returnValue = array_merge($returnValue, self::extractTreeData($node['children'], true));
        	}
        }
        
        

        return (array) $returnValue;
    }

}

?>