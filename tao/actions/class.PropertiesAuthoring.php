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
 * Copyright (c) 2015 Open Assessment Technologies S.A.
 */

/**
 * Regrouping all actions related to authoring
 * of properties
 */
class tao_actions_PropertiesAuthoring extends tao_actions_CommonModule {

    /**
     * 
     */
    public function index()
    {
        $this->defaultData();
        $clazz = new core_kernel_classes_Class($this->getRequestParameter('id'));
         
        if ($this->hasRequestParameter('property_mode')) {
            $this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
        }
         
        $myForm = $this->getClassForm($clazz);
        if ($myForm->isSubmited()) {
            if ($myForm->isValid()) {
                if ($clazz instanceof core_kernel_classes_Resource) {
                    $this->setData("selectNode", tao_helpers_Uri::encode($clazz->getUri()));
                }
                $this->setData('message', __('%s Class saved', $clazz->getLabel()));
                $this->setData('reload', true);
            }
        }
        $this->setData('formTitle', __('Edit class %s', $clazz->getLabel()));
        $this->setData('myForm', $myForm->render());
        $this->setView('form.tpl', 'tao');
    }
    
    /**
     * Render the add property sub form.
     * @throws Exception
     * @return void
     */
    public function addClassProperty()
    {
        if(!tao_helpers_Request::isAjax()){
            throw new Exception("wrong request mode");
        }
    
        $clazz = new core_kernel_classes_Class($this->getRequestParameter('id'));
    
        if($this->hasRequestParameter('index')){
            $index = $this->getRequestParameter('index');
        }
        else{
            $index = count($clazz->getProperties(false)) + 1;
        }
    
        $propMode = 'simple';
        if($this->hasSessionAttribute('property_mode')){
            $propMode = $this->getSessionAttribute('property_mode');
        }
    
        //instanciate a property form
        $propFormClass = 'tao_actions_form_'.ucfirst(strtolower($propMode)).'Property';
        if(!class_exists($propFormClass)){
            $propFormClass = 'tao_actions_form_SimpleProperty';
        }
    
        $propFormContainer = new $propFormClass($clazz, $clazz->createProperty('Property_'.$index), array('index' => $index));
        $myForm = $propFormContainer->getForm();
    
        $this->setData('data', $myForm->renderElements());
        $this->setView('blank.tpl', 'tao');
    }
    

    /**
     * Render the add property sub form.
     * @throws Exception
     * @return void
     */
    public function removeClassProperty()
    {
        $success = false;
        if(!tao_helpers_Request::isAjax()){
            throw new Exception("wrong request mode");
        }
        
        $class = new core_kernel_classes_Class($this->getRequestParameter('classUri'));
        $property = new core_kernel_classes_Property($this->getRequestParameter('uri'));
    
        //delete property mode
        foreach($class->getProperties() as $classProperty) {
            if ($classProperty->equals($property)) {
    
                //delete property and the existing values of this property
                if($property->delete(true)) {
                    $success = true;
                    break;
                }
            }
        }
        
        if ($success) {
            return $this->returnJson(array(
                'success' => true
            ));
        } else {
            $this->returnError(__('Unable to remove the property.'));
        }
    }
    

    /**
     * Create an edit form for a class and its property
     * and handle the submited data on save
     *
     * @param core_kernel_classes_Class    $clazz
     * @param core_kernel_classes_Resource $resource
     * @return tao_helpers_form_Form the generated form
     */
    public function getClassForm(core_kernel_classes_Class $clazz)
    {
    
        $propMode = 'simple';
        if($this->hasSessionAttribute('property_mode')){
            $propMode = $this->getSessionAttribute('property_mode');
        }
    
        $options = array(
            'property_mode' => $propMode,
            'topClazz' => new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE)
        );
        $formContainer = new tao_actions_form_Clazz($clazz, $clazz, $options);
        $myForm = $formContainer->getForm();
    
        if($myForm->isSubmited()){
            if($myForm->isValid()){
                //get the data from parameters
                $data = $this->getRequestParameters();
    
                // get class data and save them
                if(isset($data['class'])){
                    $classValues = array();
                    foreach($data['class'] as $key => $value){
                        $classKey =  tao_helpers_Uri::decode($key);
                        $classValues[$classKey] =  tao_helpers_Uri::decode($value);
                    }
                    
                    $this->bindProperties($clazz, $classValues);
                }
    
                //save all properties values
                if(isset($data['properties'])){
                    foreach($data['properties'] as $i => $propertyValues) {
                        if($propMode === 'simple') {
                            $this->saveSimpleProperty($propertyValues);
                        } else {
                            $this->saveAdvProperty($propertyValues);
                        }
                    }
                }
    
            }
        }
        return $myForm;
    }
    
    /**
     * Default property handling
     * 
     * @param array $propertyValues
     */
    protected function saveSimpleProperty($propertyValues)
    {
        $propertyMap = tao_helpers_form_GenerisFormFactory::getPropertyMap();
        $type = $propertyValues['type'];
        $range = (isset($propertyValues['range']) ? tao_helpers_Uri::decode(trim($propertyValues['range'])) : null);
        unset($propertyValues['type']);
        unset($propertyValues['range']);
        
        if (isset($propertyMap[$type])) {
            $values[PROPERTY_WIDGET] = $propertyMap[$type]['widget'];
        }
        
        foreach($propertyValues as $key => $value){
            $values[tao_helpers_Uri::decode($key)] = tao_helpers_Uri::decode($value);
        
        }
        $property = new core_kernel_classes_Property($values['uri']);
        unset($values['uri']);
        $this->bindProperties($property, $values);
        
        // set the range
        $property->removePropertyValues(new core_kernel_classes_Property(RDFS_RANGE));
        if(!empty($range)) {
            $property->setRange(new core_kernel_classes_Class($range));
        } elseif (isset($propertyMap[$type]) && !empty($propertyMap[$type]['range'])) {
            $property->setRange(new core_kernel_classes_Class($propertyMap[$type]['range']));
        }
        
        // set cardinality
        if(isset($propertyMap[$type]['multiple'])) {
            $property->setMultiple($propertyMap[$type]['multiple'] == GENERIS_TRUE);
        }
    }
    
    /**
     * Advanced property handling
     *
     * @param array $propertyValues
     */
    protected function saveAdvProperty($propertyValues)
    {
        // might break using hard
        $range = array();
        foreach($propertyValues as $key => $value){
            if(is_array($value)){
                // set the range
                foreach($value as $v){
                    $range[] = new core_kernel_classes_Class(tao_helpers_Uri::decode($v));
                }
            }
            else{
                $values[tao_helpers_Uri::decode($key)] = tao_helpers_Uri::decode($value);
            }
        
        }
        $property = new core_kernel_classes_Property($values['uri']);
        unset($values['uri']);
        $property->removePropertyValues(new core_kernel_classes_Property(RDFS_RANGE));
        if(!empty($range)){
            foreach($range as $r){
                $property->setRange($r);
            }
        }
        $this->bindProperties($property, $values);
    }
    
    /**
     * Helper to save class and properties
     * 
     * @param core_kernel_classes_Resource $resource
     * @param array $values
     */
    protected function bindProperties(core_kernel_classes_Resource $resource, $values) {
        $binder = new tao_models_classes_dataBinding_GenerisInstanceDataBinder($resource);
        $binder->bind($values);
    }
}
