<?php
use oat\tao\model\lock\LockManager;
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Items Controller provide actions performed from url resolution
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoItems_actions_Items extends tao_actions_SaSModule
{

    /**
     * constructor: initialize the service and the default data
     * @return  Items
     */
    public function __construct(){

        parent::__construct();

        //the service is initialized by default
        $this->service = taoItems_models_classes_ItemsService::singleton();
        $this->defaultData();
    }

    /**
     * overwrite the parent defaultData, adding the item label to be sent to the view
     */
    protected function defaultData(){
        parent::defaultData();
        if($this->hasRequestParameter('uri')){
            $uri = $this->getRequestParameter('uri');
            $classUri = $this->getRequestParameter('classUri');
            if(!empty($uri)){
                $item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($uri));
                $this->setData('label', $item->getLabel());
                $this->setData('authoringUrl', _url('authoring', 'Items', 'taoItems', array('uri' => $uri, 'classUri' => $classUri)));
                $this->setData('previewUrl', $this->getClassService()->getPreviewUrl($item));
            }
        }
    }

    /*
     * conveniance methods
     */

    /**
     * (non-PHPdoc)
     * @see tao_actions_RdfController::getClassService()
     */
    protected function getClassService(){
        return taoItems_models_classes_ItemsService::singleton();
    }

    /*
     * controller actions
     */


    /**
     * overwrite the parent addInstance to add the requiresRight only in Items
     * @requiresRight id WRITE
     */
    public function addInstance(){
        parent::addInstance();
    }
    
    /**
     * overwrite the parent addSubClass to add the requiresRight only in Items
     * @requiresRight id WRITE
     */
    public function addSubClass(){
        parent::addSubClass();
    }
    
    /**
     * overwrite the parent cloneInstance to add the requiresRight only in Items
     * @see tao_actions_TaoModule::cloneInstance()
     * @requiresRight uri READ
     * @requiresRight classUri WRITE
     */
    public function cloneInstance()
    {
        return parent::cloneInstance();
    }
    
    /**
     * overwrite the parent moveInstance to add the requiresRight only in Items
     * @see tao_actions_TaoModule::moveInstance()
     * @requiresRight uri WRITE
     * @requiresRight destinationClassUri WRITE
     */
    public function moveInstance()
    {
        return parent::moveInstance();
    }
    
    /**
     * overwrite the parent getOntologyData to add the requiresRight only in Items
     * @see tao_actions_TaoModule::getOntologyData()
     * @requiresRight classUri READ
     */
    public function getOntologyData()
    {
        return parent::getOntologyData();
    }
    
    /**
     * overwrite the parent getOntologyData to add the requiresRight only in Items
     * @see tao_actions_TaoModule::removeClassProperty()
     * @requiresRight classUri WRITE
     */
    public function removeClassProperty()
    {
        return parent::removeClassProperty();
    }
    
    /**
     * edit an item instance
     * @requiresRight id READ
     */
    public function editItem(){

        $itemClass = $this->getCurrentClass();
        $item = $this->getCurrentInstance();

        if(!$this->isLocked($item, 'item_locked.tpl')){

            // my lock
            $lock = LockManager::getImplementation()->getLockData($item);
            if (!is_null($lock) && $lock->getOwnerId() == common_session_SessionManager::getSession()->getUser()->getIdentifier()) {
                $this->setData('lockDate', $lock->getCreationTime());
                $this->setData('id', $item->getUri());
            }
            
            $formContainer = new taoItems_actions_form_Item($itemClass, $item);
            $myForm = $formContainer->getForm();
            
            if ($this->hasWriteAccess($item->getUri())) {

                if($myForm->isSubmited() && $this->hasWriteAccess($item->getUri())){
                    if($myForm->isValid()){
    
                        $properties = $myForm->getValues();
                        unset($properties[TAO_ITEM_CONTENT_PROPERTY]);
                        unset($properties['warning']);
    
                        //bind item properties and set default content:
                        $binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($item);
                        $item = $binder->bind($properties);
                        $item = $this->getClassService()->setDefaultItemContent($item);
    
                        //if item label has been changed, do not use getLabel() to prevent cached value from lazy loading
                        $label = $item->getOnePropertyValue(new core_kernel_classes_Property(RDFS_LABEL));
                        $this->setData("selectNode", tao_helpers_Uri::encode($item->getUri()));
                        $this->setData('label', ($label != null) ? $label->literal : '');
                        $this->setData('message', __('Item saved'));
                        $this->setData('reload', true);
                    }
                }
            } else {
                $myForm->setActions(array());
            }
            
            $currentModel = $this->getClassService()->getItemModel($item);
            $hasPreview = false;
            $hasModel   = false;
            if(!empty($currentModel)) {
                $hasModel = true;
                $isDeprecated = $this->getClassService()->hasModelStatus($item, array(TAO_ITEM_MODEL_STATUS_DEPRECATED));
                $hasPreview = !$isDeprecated && $this->getClassService()->hasItemContent($item);
            }

            $myForm->removeElement(tao_helpers_Uri::encode(TAO_ITEM_CONTENT_PROPERTY));

            $this->setData('isPreviewEnabled', $hasPreview);
            $this->setData('isAuthoringEnabled', $hasModel);

            $this->setData('formTitle', __('Edit Item'));
            $this->setData('myForm', $myForm->render());

            $this->setView('Items/editItem.tpl');
        }
    }

    /**
     * Edit a class
     * @requiresRight id READ 
     */
    public function editItemClass(){
        $clazz = new core_kernel_classes_Class($this->getRequestParameter('id'));

        if($this->hasRequestParameter('property_mode')){
            $this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
        }

        $myForm = $this->getClassForm($clazz, $this->getClassService()->getRootClass());
        
        if ($this->hasWriteAccess($clazz->getUri())) {
            if($myForm->isSubmited()){
                if($myForm->isValid()){
                    if($clazz instanceof core_kernel_classes_Resource){
                        $this->setData("selectNode", tao_helpers_Uri::encode($clazz->getUri()));
                    }
                    $this->setData('message', __('Class saved'));
                    $this->setData('reload', true);
                }
            }
        } else {
            $myForm->setActions(array());
        }
        $this->setData('formTitle', __('Edit item class'));
        $this->setData('myForm', $myForm->render());
        $this->setView('form.tpl', 'tao');
    }

    /**
     * delete an item
     * called via ajax
     * @requiresRight id WRITE 
     * @return void
     * @throws Exception
     */
    public function deleteItem()
    {
        return parent::deleteResource();
    }

    /**
     * delete an item class
     * called via ajax
     * @requiresRight id WRITE
     * @throws Exception
     */
    public function deleteClass()
    {
        return parent::deleteClass();
    }

    /**
     * @see TaoModule::translateInstance
     * @requiresRight uri WRITE 
     * @return void
     */
    public function translateInstance(){
        parent::translateInstance();
        $this->setView('form.tpl', 'tao');
    }

    /**
     * Display the Item.ItemContent property value.
     * It's used by the authoring runtime/tools to retrieve the content
     * @return void
     */
    public function getItemContent(){

        $this->setContentHeader('text/xml');

        try{
            //output direclty the itemContent as XML
            print $this->getClassService()->getItemContent($this->getCurrentInstance());
        }catch(Exception $e){
            //print an empty response
            print '<?xml version="1.0" encoding="utf-8" ?>';
            if(DEBUG_MODE){
                print '<exception><![CDATA[';
                print $e;
                print ']]></exception>';
            }
        }

        return;
    }

    /**
     * Download the content of the item in parameter
     * @requiresRight uri READ
     * @deprecated
     */
    public function downloadItemContent(){

        $instance = $this->getCurrentInstance();
        if($this->getClassService()->isItemModelDefined($instance)){

            $itemModel = $instance->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
            $filename = $instance->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_SOURCENAME_PROPERTY));
            if(is_null($filename)){
                $filename = $itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY));
            }

            $itemContent = $this->getClassService()->getItemContent($instance);
            $size = strlen($itemContent);

            $this->setContentHeader('text/xml');
            header("Content-Length: $size");
            header("Content-Disposition: attachment; filename=\"{$filename}\"");
            header("Expires: 0");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            print $itemContent;
            return;
        }
    }

    /**
     * Item Authoring tool loader action
     * @requiresRight id WRITE
     */
    public function authoring(){
        $item = new core_kernel_classes_Resource($this->getRequestParameter('id'));

        if(!$this->isLocked($item, 'item_locked.tpl')){
            
            $this->setData('error', false);
            try{

                $itemModel = $this->getClassService()->getItemModel($item);
                if(!is_null($itemModel)){
                    $itemModelImpl = $this->getClassService()->getItemModelImplementation($itemModel);
                    $authoringUrl = $itemModelImpl->getAuthoringUrl($item);
                    if(!empty($authoringUrl)){
                        LockManager::getImplementation()->setLock($item, common_session_SessionManager::getSession()->getUser()->getIdentifier());

                        return $this->forwardUrl($authoringUrl);
                    }
                }
                throw new common_exception_NoImplementation();
                $this->setData('instanceUri', tao_helpers_Uri::encode($item->getUri(), false));

            } catch (Exception $e) {
                $this->setData('error', true);
                //build clear error or warning message:
                if(!empty($itemModel) && $itemModel instanceof core_kernel_classes_Resource){
                    $errorMsg = __('No item authoring tool available for the selected type of item: %s'.$itemModel->getLabel());
                }else{
                    $errorMsg = __('No item type selected for the current item.')." {$item->getLabel()} ".__('Please select first the item type!');
                }
                $this->setData('errorMsg', $errorMsg);
            }
        }
    }

    /**
     * Load an item external media
     * It prevents to get it direclty in the data folder that access is denied
     * @requiresRight uri READ
     * @deprecated
     */
    public function getMediaResource(){

        if($this->hasRequestParameter('path')){

            $item = null;
            if($this->hasRequestParameter('uri') && $this->hasRequestParameter('classUri')){
                $item = $this->getCurrentInstance();
            }else if($this->hasSessionAttribute('uri') && $this->hasSessionAttribute('classUri')){
                $classUri = tao_helpers_Uri::decode($this->getSessionAttribute('classUri'));
                if($this->getClassService()->isItemClass(new core_kernel_classes_Class($classUri))){
                    $item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getSessionAttribute('uri')));
                }
            }

            if(!is_null($item)){

                $path = urldecode($this->getRequestParameter('path'));
                if(!tao_helpers_File::securityCheck($path)){
                    throw new Exception('Unauthorized path '.$path);
                }
                if(preg_match('/(.)+\/filemanager\/views\/data\//i', $path)){
                    // check if the file is linked to the file manager
                    $resource = preg_replace('/(.)+\/filemanager\/views\/data\//i', ROOT_PATH.'/filemanager/views/data/', $path);
                }else{
                    // look in the item's dedicated folder. it should be a resource
                    // that is local to the item, not it the file manager
                    // $folder is the item's dedicated folder path, $path the path to the resource, relative to $folder
                    $folder = $this->getClassService()->getItemFolder($item);
                    $resource = tao_helpers_File::concat(array($folder, $path));
                }

                if(file_exists($resource)){
                    $mimeType = tao_helpers_File::getMimeType($resource);

                    //allow only images, video, flash (and css?)
                    if(preg_match("/^(image|video|audio|application\/x-shockwave-flash)/", $mimeType)){
                        header("Content-Type: $mimeType; charset utf-8");
                        print trim(file_get_contents($resource));
                    }
                }
            }
        }
    }
}
