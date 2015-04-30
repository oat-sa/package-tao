<?php
use oat\tao\model\lock\LockManager;
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Service methods to manage the Items business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 
 */
class taoItems_models_classes_ItemsService extends tao_models_classes_ClassService
{

    /**
     * The RDFS top level item class
     *
     * @access protected
     * @var Class
     */
    protected $itemClass = null;

    /**
     * the instance of the itemModel property
     *
     * @access protected
     * @var Property
     */
    protected $itemModelProperty = null;

    /**
     * the instance of the itemContent property
     *
     * @access public
     * @var Property
     */
    public $itemContentProperty = null;

    /**
     * key to use to store dthe default filesource
     * to be used in for new items
     *
     * @access private
     * @var string
     */

    const CONFIG_DEFAULT_FILESOURCE = 'defaultItemFileSource';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return void
     */
    protected function __construct(){
        parent::__construct();
        $this->itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
        $this->itemModelProperty = new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY);
        $this->itemContentProperty = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
    }

    public function getRootClass(){
        return $this->itemClass;
    }

    /**
     * get an item subclass by uri. 
     * If the uri is not set, it returns the  item class (the top level class.
     * If the uri don't reference an item subclass, it returns null
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string uri
     * @return core_kernel_classes_Class
     * @deprecated
     */
    public function getItemClass($uri = ''){
        $returnValue = null;

        if(empty($uri) && !is_null($this->itemClass)){
            $returnValue = $this->itemClass;
        }else{
            $clazz = new core_kernel_classes_Class($uri);
            if($this->isItemClass($clazz)){
                $returnValue = $clazz;
            }
        }

        return $returnValue;
    }

    /**
     * check if the class is a or a subclass of an Item
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  core_kernel_classes_Class clazz
     * @return boolean
     */
    public function isItemClass(core_kernel_classes_Class $clazz){
        $returnValue = (bool) false;

        if($this->itemClass->getUri() == $clazz->getUri()){
            return true;
        }

        foreach($clazz->getParentClasses(true) as $parent){

            if($parent->getUri() == $this->itemClass->getUri()){
                $returnValue = true;
                break;
            }
        }

        return (bool) $returnValue;
    }

    /**
     * please call deleteResource() instead
     * @deprecated
     */
    public function deleteItem(core_kernel_classes_Resource $item){
        return $this->deleteResource($item);
    }
    
    /**
     * delete an item
     * @param core_kernel_classes_Resource $resource
     * @throws common_exception_Unauthorized
     * @return boolean
     */
    public function deleteResource(core_kernel_classes_Resource $resource)
    {
        if (LockManager::getImplementation()->isLocked($resource)) {
            $userId = common_session_SessionManager::getSession()->getUser()->getIdentifier();
            LockManager::getImplementation()->releaseLock($resource, $userId);
        }
        
        return $this->deleteItemContent($resource) && parent::deleteResource($resource);
    }

    /**
     * delete an item class or subclass
     * @deprecated
     */
    public function deleteItemClass(core_kernel_classes_Class $clazz)
    {
        return $this->deleteClass($clazz);
    }

    /**
     * Short description of method getDefaultItemFolder
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  core_kernel_classes_Resource item
     * @param  string lang
     * @return string
     */
    public function getDefaultItemFolder(core_kernel_classes_Resource $item, $lang = ''){
        $returnValue = (string) '';

        if(!is_null($item)){

            $lang = empty($lang) ? $this->getSessionLg() : $lang;

            $itemRepo = $this->getDefaultFileSource();
            $repositoryPath = $itemRepo->getPath();
            $repositoryPath = substr($repositoryPath, strlen($repositoryPath) - 1, 1) == DIRECTORY_SEPARATOR ? $repositoryPath : $repositoryPath.DIRECTORY_SEPARATOR;
			$returnValue = $repositoryPath.tao_helpers_Uri::getUniqueId($item->getUri()).DIRECTORY_SEPARATOR.'itemContent'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR;
        }

        return (string) $returnValue;
    }

    /**
     * define the content of item to be inserted by default (to prevent null
     * after creation).
     * The item content's folder is created.
     *
     * @access public
     * @author Bertrand Chevrier <bertrand@taotesting.com>
     * @param  core_kernel_classes_Resource item
     * @return core_kernel_classes_Resource the same item
     */
    public function setDefaultItemContent(core_kernel_classes_Resource $item){

        if(!is_null($item)){

            //we create the item folder by default. 
            //TODO this should be implemented through the filesystem abstraction but it doesn't work for directory
            $itemFolder = $this->getDefaultItemFolder($item);
            if(!file_exists($itemFolder) && strpos($itemFolder, ROOT_PATH) >= 0){
                if(!mkdir($itemFolder, 0770, true)){
                    common_Logger::w('Unable to create default item folder at location : ' . $itemFolder);
                }
            }
        }

        return $item;
    }

    /**
     * Enables you to get the content of an item, 
     * usually an xml string
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  boolean preview
     * @param  string lang
     * @return string
     */
    public function getItemContent(core_kernel_classes_Resource $item, $lang = ''){
        $returnValue = (string) '';

        common_Logger::i('Get itemContent for item '.$item->getUri());

        if(!is_null($item)){

            $itemContent = null;

            if(empty($lang)){
                $itemContents = $item->getPropertyValuesCollection($this->itemContentProperty);
            }else{
                $itemContents = $item->getPropertyValuesByLg($this->itemContentProperty, $lang);
            }

            if($itemContents->count() > 0){
                $itemContent = $itemContents->get(0);
            }
            //if no itemContent is set for the lang get the default one and copy it into a new repository
            else if(!empty($lang)){
                $itemContents = $item->getPropertyValuesCollection($this->itemContentProperty);
                if($itemContents->count() > 0){
                    $itemContent = $itemContents->get(0);
                    $this->setDefaultItemContent($item, $itemContent, $lang);
                    tao_helpers_File::copy($this->getItemFolder($item, DEFAULT_LANG), $this->getItemFolder($item, $lang));
                }
            }
            if(!is_null($itemContent) && $this->isItemModelDefined($item)){

                if(core_kernel_file_File::isFile($itemContent)){

                    $file = new core_kernel_file_File($itemContent->getUri());
                    $returnValue = file_get_contents($file->getAbsolutePath());
                    if($returnValue == false){
                        common_Logger::w('File '.$file->getAbsolutePath().' not found for fileressource '.$itemContent->getUri());
                    }
                }
            }else{
                common_Logger::w('No itemContent for item '.$item->getUri());
            }
        }

        return (string) $returnValue;
    }

    /**
     * Check if the item has an itemContent Property
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  string lang
     * @return boolean
     */
    public function hasItemContent(core_kernel_classes_Resource $item, $lang = ''){
        $returnValue = (bool) false;

        if(!is_null($item)){

            if(empty($lang)){
                $lang = $this->getSessionLg();
            }

            $itemContents = $item->getPropertyValuesByLg($this->itemContentProperty, $lang);
            $returnValue = ($itemContents->count() > 0);
        }

        return (bool) $returnValue;
    }

    /**
     * Short description of method setItemContent
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  string content
     * @param  string lang
     * @param  string commitMessage
     * @return boolean
     */
    public function setItemContent(core_kernel_classes_Resource $item, $content, $lang = '', $commitMessage = ''){
        $returnValue = (bool) false;

        if(is_null($item) && !$this->isItemModelDefined($item)){
            throw new common_exception_Error('No item or itemmodel in '.__FUNCTION__);
        }

        $lang = empty($lang) ? $lang = $this->getSessionLg() : $lang;
        $itemModel = $item->getUniquePropertyValue($this->itemModelProperty);
        $dataFile = (string) $itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY));

        if($this->hasItemContent($item, $lang)){

            $itemContents = $item->getPropertyValuesByLg($this->itemContentProperty, $lang);
            $itemContent = $itemContents->get(0);
            if(!core_kernel_file_File::isFile($itemContent)){
                throw new common_Exception('Item '.$item->getUri().' has none file itemContent');
            }
            $file = new core_kernel_versioning_File($itemContent);
            $returnValue = $file->setContent($content);
        }else{

            $repository = $this->getDefaultFileSource();
            $file = $repository->createFile(
                    $dataFile, tao_helpers_Uri::getUniqueId($item->getUri()).DIRECTORY_SEPARATOR.'itemContent'.DIRECTORY_SEPARATOR.$lang
            );
            $item->setPropertyValueByLg($this->itemContentProperty, $file->getUri(), $lang);
            $file->setContent($content);
            $returnValue = $file->add(true, true);
        }

        if($commitMessage != 'HOLD_COMMIT'){//hack to control commit or not
            $returnValue = $file->commit($commitMessage);
        }

        return (bool) $returnValue;
    }

    /**
     * Check if the Item has on of the itemModel property in the models array
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  array models the list of URI of the itemModel to check
     * @return boolean
     */
    public function hasItemModel(core_kernel_classes_Resource $item, $models){
        $returnValue = (bool) false;

        $itemModel = $item->getOnePropertyValue($this->itemModelProperty);
        if($itemModel instanceof core_kernel_classes_Resource){
            if(in_array($itemModel->getUri(), $models)){
                $returnValue = true;
            }
        }

        return (bool) $returnValue;
    }

    /**
     * Check if the itemModel has been defined for that item
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return boolean
     */
    public function isItemModelDefined(core_kernel_classes_Resource $item){
        $returnValue = (bool) false;

        if(!is_null($item)){

            $model = $item->getOnePropertyValue($this->itemModelProperty);
            if($model instanceof core_kernel_classes_Literal){
                if(strlen((string) $model) > 0){
                    $returnValue = true;
                }
            }else if(!is_null($model)){
                $returnValue = true;
            }
        }

        return (bool) $returnValue;
    }

    /**
     * Get the runtime associated to the item model.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function getModelRuntime(core_kernel_classes_Resource $item){
        $returnValue = null;

        if(!is_null($item)){
            $itemModel = $item->getOnePropertyValue($this->itemModelProperty);
            if(!is_null($itemModel)){
                $returnValue = $itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_RUNTIME_PROPERTY));
            }
        }

        return $returnValue;
    }

    /**
     * Short description of method hasModelStatus
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  array status
     * @return boolean
     */
    public function hasModelStatus(core_kernel_classes_Resource $item, $status){
        $returnValue = (bool) false;

        if(!is_null($item)){
            if(!is_array($status) && is_string($status)){
                $status = array($status);
            }
            try{
                $itemModel = $item->getOnePropertyValue($this->itemModelProperty);
                if($itemModel instanceof core_kernel_classes_Resource){
                    $itemModelStatus = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_STATUS_PROPERTY));
                    if(in_array($itemModelStatus->getUri(), $status)){
                        $returnValue = true;
                    }
                }
            }catch(common_exception_EmptyProperty $ce){
                $returnValue = false;
            }
        }

        return (bool) $returnValue;
    }

    /**
     * render used for deploy and preview
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return string
     * @throws taoItems_models_classes_ItemModelException
     */
    public function render(core_kernel_classes_Resource $item, $language){

        $itemModel = $this->getItemModel($item);
        if(is_null($itemModel)){
            throw new common_exception_NoImplementation('No item model for item '.$item->getUri());
        }
        $impl = $this->getItemModelImplementation($itemModel);
        if(is_null($impl)){
            throw new common_exception_NoImplementation('No implementation for model '.$itemModel->getUri());
        }
        return $impl->render($item, $language);
    }

    /**
     * Woraround for item content
     * (non-PHPdoc)
     * @see tao_models_classes_GenerisService::cloneInstanceProperty()
     */
    protected function cloneInstanceProperty( core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination, core_kernel_classes_Property $property) {
        if ($property->getUri() == TAO_ITEM_CONTENT_PROPERTY) {
            return $this->cloneItemContent($source, $destination, $property);
        } else {
            return parent::cloneInstanceProperty($source, $destination, $property);
        }
    }
    
    protected function cloneItemContent($source, $destination, $property) {
        $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
        foreach($source->getPropertyValuesCollection($property)->getIterator() as $propertyValue){
            $file = new core_kernel_versioning_File($propertyValue->getUri());
            $repo = $file->getRepository();
            $relPath = basename($file->getAbsolutePath());
            if(!empty($relPath)){
                $newPath = tao_helpers_File::concat(array($this->getItemFolder($destination), $relPath));
                common_Logger::i('copy '.dirname($file->getAbsolutePath()).' to '.dirname($newPath));
                tao_helpers_File::copy(dirname($file->getAbsolutePath()), dirname($newPath), true);
                if(file_exists($newPath)){
                    $subpath = substr($newPath, strlen($repo->getPath()));
                    $newFile = $repo->createFile(
                        (string) $file->getOnePropertyValue($fileNameProp), dirname($subpath).'/'
                    );
                    $destination->setPropertyValue($property, $newFile->getUri());
                    $newFile->add(true, true);
                    $newFile->commit('Clone of '.$source->getUri(), true);
                }
            }
        }
    }
    
    public function getPreviewUrl(core_kernel_classes_Resource $item, $lang = '') {
        $itemModel = $this->getItemModel($item);
        if(is_null($itemModel)){
            return null;
        }
        return $this->getItemModelImplementation($itemModel)->getPreviewUrl($item, $lang);
    }

    /**
     * Short description of method getItemModel
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function getItemModel(core_kernel_classes_Resource $item){
        $returnValue = null;

        $itemModel = $item->getOnePropertyValue($this->itemModelProperty);
        if($itemModel instanceof core_kernel_classes_Resource){
            $returnValue = $itemModel;
        }

        return $returnValue;
    }
    
    /**
     * Set the model of an item
     * 
     * @param core_kernel_classes_Resource $item
     * @param core_kernel_classes_Resource $model
     * @return boolean
     */
    public function setItemModel(core_kernel_classes_Resource $item, core_kernel_classes_Resource $model)
    {
        $modelProp = new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY);
        return $item->editPropertyValues($modelProp, $model);
    }
    

    /**
     * Rertrieve current user's language from the session object to know where
     * item content should be located
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getSessionLg(){

        $sessionLang = \common_session_SessionManager::getSession()->getDataLanguage();
        if(empty($sessionLang)){
            throw new Exception('the data language of the user cannot be found in session');
        }

        return (string) $sessionLang;
    }

    /**
     * Deletes the content but does not unreference it
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  core_kernel_classes_Resource item
     * @return boolean
     */
    public function deleteItemContent(core_kernel_classes_Resource $item){
        $returnValue = (bool) false;

        //delete the folder for all languages!
        foreach($item->getUsedLanguages($this->itemContentProperty) as $lang){
            $files = $item->getPropertyValuesByLg($this->itemContentProperty, $lang);
            foreach($files->getIterator() as $file){
                if ($file instanceof core_kernel_classes_Resource) {
                    $file = new core_kernel_file_File($file);
                    if(core_kernel_versioning_File::isVersionedFile($file)){
                        $file = new core_kernel_versioning_File($file);
                    }
                    try{
                        $file->delete();
                    }catch(core_kernel_versioning_exception_FileUnversionedException $e){
                        // file was not versioned after all, ignore in delte
                    }
                }
            }
        }

        $returnValue = true;

        return (bool) $returnValue;
    }

    /**
     * Short description of method getItemModelImplementation
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource itemModel
     * @return taoItems_models_classes_itemModel
     */
    public function getItemModelImplementation(core_kernel_classes_Resource $itemModel){
        $returnValue = null;

        $services = $itemModel->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ITEM_MODEL_SERVICE));
        if(count($services) > 0){
            if(count($services) > 1){
                throw new common_exception_Error('Conflicting services for itemmodel '.$itemModel->getLabel());
            }
            $serviceName = (string) current($services);
            if(class_exists($serviceName) && in_array('taoItems_models_classes_itemModel', class_implements($serviceName))){
                $returnValue = new $serviceName();
            }else{
                throw new common_exception_Error('Item model service '.$serviceName.' not found, or not compatible for item model '.$itemModel->getLabel());
            }
        }else{
            common_Logger::d('No implementation for '.$itemModel->getLabel());
        }

        return $returnValue;
    }

    /**
     * Short description of method isItemVersioned
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return boolean
     */
    public function isItemVersioned(core_kernel_classes_Resource $item){
        $returnValue = (bool) false;

        $files = $item->getPropertyValues($this->itemContentProperty);
        foreach($files as $file){
            // theoreticaly this should always be no or a single file 
            if($file->hasType(new core_kernel_classes_Class(CLASS_GENERIS_FILE))){
                $returnValue = true;
            }
        }

        return (bool) $returnValue;
    }

    /**
     * Short description of method getItemFolder
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  string lang
     * @return string
     */
    public function getItemFolder(core_kernel_classes_Resource $item, $lang = ''){
        $returnValue = (string) '';

        if($lang === ''){
            $files = $item->getPropertyValues(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
        }else{
            $files = $item->getPropertyValuesByLg(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY), $lang)->toArray();
        }
        if(count($files) == 0){
            // no content found assign default
            $returnValue = $this->getDefaultItemFolder($item, $lang);
        }else{
            if(count($files) > 1){
                throw new common_Exception(__METHOD__.': Item '.$item->getUri().' has multiple.');
            }
            $content = new core_kernel_file_File(current($files));
            $returnValue = dirname($content->getAbsolutePath()).DIRECTORY_SEPARATOR;
        }

        return (string) $returnValue;
    }
    
    public function getCompilerClass(core_kernel_classes_Resource $item) {
        $itemModel = $this->getItemModel($item);
        if(is_null($itemModel)){
            throw new common_exception_Error('undefined itemmodel for test '.$item->getUri());
        }
        return $this->getItemModelImplementation($itemModel)->getCompilerClass();;
    }

    /**
     * sets the filesource to use for new items
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Repository filesource
     * @return mixed
     */
    public function setDefaultFilesource(core_kernel_versioning_Repository $filesource){
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
        $ext->setConfig(self::CONFIG_DEFAULT_FILESOURCE, $filesource->getUri());
    }

    /**
     * returns the filesource to use for new items
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return core_kernel_versioning_Repository
     */
    public function getDefaultFileSource(){
        $returnValue = null;

        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
        $uri = $ext->getConfig(self::CONFIG_DEFAULT_FILESOURCE);
        if(!empty($uri)){
            $returnValue = new core_kernel_versioning_Repository($uri);
        }else{
            throw new common_Exception('No default repository defined for Items storage.');
        }

        return $returnValue;
    }
    
    /**
     * Get items of a specific model
     * @param string|core_kernel_classes_Resource $itemModel - the item model URI
     * @return core_kernel_classes_Resource[] the found items
     */
    public function getAllByModel($itemModel){
        if(!empty($itemModel)){
            $uri = ($itemModel instanceof core_kernel_classes_Resource) ? $itemModel->getUri() : $itemModel;
            return $this->itemClass->searchInstances(array(
                    $this->itemModelProperty->getUri() => $uri
                ), array(
                    'recursive' => true
                ));
        }
        return array();
    }

}
