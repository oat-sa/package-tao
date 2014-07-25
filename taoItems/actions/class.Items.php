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
?>
<?php
/**
 * Items Controller provide actions performed from url resolution
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoItems_actions_Items extends tao_actions_SaSModule
{

	/**
	 * constructor: initialize the service and the default data
	 * @return  Items
	 */
	public function __construct()
	{

		parent::__construct();

		//the service is initialized by default
		$this->service = taoItems_models_classes_ItemsService::singleton();
		$this->defaultData();

	}
	
	/**
	 * overwrite the parent defaultData, adding the item label to be sent to the view
	 */
	protected function defaultData()
	{
		parent::defaultData();
		if($this->hasRequestParameter('uri')) {
			$uri = $this->getRequestParameter('uri');
			if (!empty($uri)) {
				$item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($uri));
				$this->setData('label', $item->getLabel());
			}
		}
	}
/*
 * conveniance methods
 */

	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getClassService()
	{
		return taoItems_models_classes_ItemsService::singleton();
	}

/*
 * controller actions
 */


	/**
	 * edit an item instance
	 */
	public function editItem()
	{

		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentInstance();

		$formContainer = new taoItems_actions_form_Item($itemClass, $item);
		$myForm = $formContainer->getForm();

		/*
		 * crapy way to add the status of the item model
		 * @todo set this in the taoItems_actions_form_Item
		 **/
		$deprecatedOptions = array();
		$statusProperty = new core_kernel_classes_Property(TAO_ITEM_MODEL_STATUS_PROPERTY);
		$itemModelElt = $myForm->getElement(tao_helpers_Uri::encode(TAO_ITEM_MODEL_PROPERTY));
		$options = $itemModelElt->getOptions();
		foreach($options as $optUri => $optLabel){
			$model = new core_kernel_classes_Resource(tao_helpers_Uri::decode($optUri));
			$status = $model->getOnePropertyValue($statusProperty);
			$statusLabel = (!is_null($status))?trim($status->getLabel()):'';
			if(!empty($statusLabel)){
				//in the case it is a stable one, implicit (may be misleading otherwise)
				//$options[$optUri] = $optLabel . " ($statusLabel)";
			}
			if(!is_null($status)){
				if($status->getUri() == TAO_ITEM_MODEL_STATUS_DEPRECATED){
					$deprecatedOptions[] = $optUri;
				}
			}
		}
		$itemModelElt->setOptions($options);
		 
		$this->setData('deprecatedOptions', json_encode($deprecatedOptions));

		$hasAuthoring = false;
		$hasPreview = false;
		$modelUri = $itemModelElt->getEvaluatedValue();
		if (is_string($modelUri) && !empty($modelUri)) {
			$currentModel = new core_kernel_classes_Resource($modelUri);
			$authoring = $currentModel->getPropertyValues(new core_kernel_classes_Property(TAO_ITEM_MODEL_AUTHORING_PROPERTY));
    		$isDeprecated =  $this->service->hasModelStatus($item, array(TAO_ITEM_MODEL_STATUS_DEPRECATED));
    		$hasPreview = !$isDeprecated;
			if (count($authoring) > 0 && !$isDeprecated) {
			    $hasAuthoring = true;
			}
		}

		if($myForm->isSubmited()){
			if($myForm->isValid()){

				$properties = $myForm->getValues();
				unset($properties[TAO_ITEM_CONTENT_PROPERTY]);
				unset($properties['warning']);
				
				//bind item properties and set default content:
				$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($item);
				$item = $binder->bind($properties);
				$item = $this->service->setDefaultItemContent($item);
				
                //if item label has been changed, do not use getLabel() to prevent cached value from lazy loading
                $label = $item->getOnePropertyValue(new core_kernel_classes_Property(RDFS_LABEL));
				$this->setData('label', ($label != null) ? $label->literal : '');
				$this->setData('message', __('Item saved'));
				$this->setData('reload', true);
			}
		}

		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->getUri()));

		if(!$hasAuthoring){
			$myForm->removeElement(tao_helpers_Uri::encode(TAO_ITEM_CONTENT_PROPERTY));
		}

		$this->setData('uri', tao_helpers_Uri::encode($item->getUri()));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->getUri()));

		$this->setData('isPreviewEnabled', $hasPreview);
		$this->setData('isAuthoringEnabled', $hasAuthoring);

		$this->setData('formTitle', __('Edit Item'));
		$this->setData('myForm', $myForm->render());

		$this->setView('item_form.tpl');
	}

	public function itemVersionedContentIO(){

		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}

		$item = new core_kernel_classes_Resource($uri);
		$itemModel = $this->service->getItemModel($item);
		if(is_null($itemModel)){
			throw new Exception('cannot edit versioned item content if no item model is set');
		}

		$ownerInstance = $item;
		$property = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
		$propertyRange = $property->getRange();

		//get the versioned file resource
		$versionedFileResource = $ownerInstance->getOnePropertyValue($property);
		//if it does not exist already, create a new versioned file resource
		if(!$versionedFileResource instanceof core_kernel_classes_Resource){
			//if the file resource does not exist, create it
			$versionedFileResource = $propertyRange->createInstance();
			$ownerInstance->setPropertyValue($property, $versionedFileResource->getUri());
		}
		$versionedFile = new core_kernel_versioning_File($versionedFileResource->getUri());

		//create the form
		$formContainer = new taoItems_actions_form_VersionedItemContent(null
			, array(
				'instanceUri' => $versionedFile->getUri(),
				'ownerUri' => $ownerInstance->getUri(),
				'propertyUri' => $property->getUri()
			)
		);
		$myForm = $formContainer->getForm();

		//if the form was sent successfully
		if($myForm->isSubmited()){

			if($myForm->isValid()){

				// Extract data from form
				$data = $myForm->getValues();

				// Extracted values
				$content = '';
				$delete = isset($data['file_delete']) && $data['file_delete'] == '1'?true:false;
				$message = isset($data['commit_message'])?$data['commit_message']:'';
				$fileName = '';
				$filePath = $this->service->getItemFolder($item);
				$repositoryUri = $data[PROPERTY_FILE_FILESYSTEM];
				$version = isset($data['file_version']) ? $data['file_version'] : 0;

				$done = false;
				//the file is already versioned
				if($versionedFile->isVersioned()){
					if($delete){

						$versionedFile->delete();//no need to commit here (already done in the funciton implementation
						$done = $ownerInstance->removePropertyValues($property);
						$itemContentProp = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
						foreach($ownerInstance->getPropertyValues($itemContentProp) as $fileUri){
							if(common_Utils::isUri($fileUri)){
								$file = new core_kernel_versioning_File($fileUri);
								$file->delete();
							}
						}
						$done &= $ownerInstance->removePropertyValues($itemContentProp);

						$this->setData('message', __('item versioned content deleted'));
					}else if ($version) {//version = [1..n]
						//revert to a version
						$topRevision = count($myForm->getElement('file_version')->getOptions());
						if ($version < $topRevision) {
							$done = $versionedFile->revert($version, empty($message)?'Revert to TAO version '.$version : $message);
							$this->setData('message', __('revision restored : ').$version);
						}

					}
				}

				//a new content was sent
				if (!$done && isset($data['file_import']['uploaded_file'])) {
					$imported = false;
					if (file_exists($data['file_import']['uploaded_file'])) {
						$uploadedFilePath = $data['file_import']['uploaded_file'];
						switch ($itemModel->getUri()) {
							case TAO_ITEM_MODEL_QTI: {
									if (preg_match('/\.xml/i', $uploadedFilePath)) {
										$imported = $this->importQTIFile($item, $uploadedFilePath, false, $message);
									} else if (preg_match('/\.zip/i', $uploadedFilePath)) {
										$imported = $this->importQTIPACKFile($item, $uploadedFilePath, false, $message);
									} else {
										//wrong file type!
										throw new Exception(__('wrong file format'));
									}
									break;
								}
							case TAO_ITEM_MODEL_XHTML: {
									if (preg_match('/\.zip/i', $uploadedFilePath)) {
										$imported = $this->importXHTMLFile($item, $uploadedFilePath, false, $message);
									} else {
										//wrong file type!
										throw new Exception(__('wrong file format'));
									}
									break;
								}
							default: {
									$content = file_get_contents($uploadedFilePath);
									break;
								}
						}
					} else {
						throw new Exception(__('the file was not uploaded successfully'));
					}

					if ($imported) {
						$this->setData('message', __('item versioned content saved'));
					}

				}

				//refresh the page to reflect the change
				if(!count(get_data('importErrors'))){
					$ctx = Context::getInstance();
					$this->redirect(_url($ctx->getActionName(), $ctx->getModuleName(), $ctx->getExtensionName(), array(
						'uri'			=> tao_helpers_Uri::encode($ownerInstance->getUri()),
						'propertyUri'	=> tao_helpers_Uri::encode($property->getUri()),
						'message'		=> get_data('message')
					)));
				}
			}
		}

		$this->setData('formTitle', __('Manage the item versioned content').' '.$this->service->getItemModel($ownerInstance)->getLabel().' : '.$ownerInstance->getLabel());
		$this->setData('myForm', $myForm->render());

		$this->setView('form/versioned_file.tpl', 'tao');
	}

	/**
	 * action to perform on a posted QTI file
	 */
	protected function importQTIFile(core_kernel_classes_Resource $rdfItem, $uploadedFile, $forceValid = false, $commitMessage = 'QTI XML uploaded'){

		//get the services instances we will need
		$qtiService = taoQTI_models_classes_QTI_Service::singleton();

		//validate the file to import
		$qtiParser = new taoItems_models_classes_QTI_Parser($uploadedFile);
		$qtiParser->validate();
		if(!$qtiParser->isValid() && !$forceValid){
			$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
			$this->setData('importErrors', $qtiParser->getErrors());
		}
		else{
			//load the QTI item from the file
			$qtiItem = $qtiParser->load();
			if(!is_null($qtiItem)){
				//set the QTI type (security)
				$rdfItem->editPropertyValues(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_QTI);

				if($qtiService->saveDataItemToRdfItem($qtiItem, $rdfItem, '[QTI xml] '.$commitMessage)){
					$this->setData('message', __('Item imported successfully') . ' : ' .$rdfItem->getLabel());
					@unlink($uploadedFile);
					return true;
				}
			}
			$this->setData('message', __('An error occurs during the import'));
		}
		return false;
	}

	/**
	 * action to perform on a posted QTI CP file
	 * @param array $formValues the posted data
	 */
	protected function importQTIPACKFile(core_kernel_classes_Resource $rdfItem, $uploadedFile, $forceValid = false, $commitMessage = 'QTI package uploaded'){

		$returnValue = true;

		set_time_limit(200);	//the zip extraction is a long process that can exced the 30s timeout

		//get the services instances we will need
		$qtiService = taoQTI_models_classes_QTI_Service::singleton();

		//load and validate the package
		$qtiPackageParser = new taoItems_models_classes_QTI_PackageParser($uploadedFile);
		$qtiPackageParser->validate();

		if(!$qtiPackageParser->isValid() && !$forceValid){
			$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
			$this->setData('importErrors', $qtiPackageParser->getErrors());
			return $returnValue;
		}

		//extract the package
		$folder = $qtiPackageParser->extract();
		if(!is_dir($folder)){
			$this->setData('importErrorTitle', __('An error occured during the import'));
			$this->setData('importErrors', array(array('message' => __('unable to extract archive content, please check your tmp dir'))));
			return $returnValue;
		}

		//load and validate the manifest
		$qtiManifestParser = new taoItems_models_classes_QTI_ManifestParser($folder .'/imsmanifest.xml');
		$qtiManifestParser->validate();
		if(!$qtiManifestParser->isValid() && !$forceValid){
			$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
			$this->setData('importErrors', $qtiManifestParser->getErrors());
			return $returnValue;
		}

		//load the information about resources in the manifest
		$resources = $qtiManifestParser->load();
		$importedItems = 0;
		foreach($resources as $resource){

			if($resource instanceof taoItems_models_classes_QTI_Resource){

				$qtiParser = new taoItems_models_classes_QTI_Parser($folder . '/'. $resource->getItemFile());
				$qtiItem = $qtiParser->load();

				if(is_null($qtiItem) || is_null($rdfItem)){

					$this->setData('importErrorTitle', __('An error occured during the import'));
					$this->setData('importErrors', array(array('message' => __('Unable to create the item for the content '.$resource->getIdentifier().' , from file '.$resource->getItemFile()))));

					// An error occured. We should rollback the knowledge base.
					$rdfItem->delete();
					if(!$forceValid){
						break;
					}

				}else{

					//set the QTI type
					$rdfItem->editPropertyValues(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_QTI);

					//set the file in the itemContent
					$subpath = preg_quote(dirname($resource->getItemFile()), '/');

					//and copy the others resources in the runtime path
					$itemPath = $this->service->getItemFolder($rdfItem);

					foreach($resource->getAuxiliaryFiles() as $auxResource){
						$auxPath = $auxResource;
						$auxPath = preg_replace("/^$subpath\//", '', $auxResource);
						tao_helpers_File::copy($folder . '/'. $auxResource, $itemPath.'/'.$auxPath, true);
					}

					if($qtiService->saveDataItemToRdfItem($qtiItem, $rdfItem, 'HOLD_COMMIT')){
						$versionedFolder = $rdfItem->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_VERSIONED_CONTENT_PROPERTY));
						$versionedFolder = new core_kernel_versioning_File($versionedFolder->getUri());
						if($versionedFolder->add(true, true) && $versionedFolder->commit('[QTI pack] '.$commitMessage)){
							$importedItems++;
						}
					}
				}

				//one loop is enough
				//@TODO : available mutiple languages import/export with a package?
				break;
			}
		}

		if($importedItems){

			$this->setData('message', $importedItems . ' ' . __('items imported successfully'));
			$this->setData('reload', true);

			$returnValue = true;
		}

		tao_helpers_File::remove($uploadedFile);
		tao_helpers_File::remove(str_replace('.zip', '', $uploadedFile), true);

		return (bool) $returnValue;
	}

	/**
	 * import OWI items
	 */
	protected function importXHTMLFile(core_kernel_classes_Resource $rdfItem, $uploadedFile, $forceValid = false, $commitMessage = 'OWI package uploaded'){

		$returnValue = false;

		set_time_limit(200);	//the zip extraction is a long process that can exced the 30s timeout

		//load and validate the package
		$packageParser = new taoItems_models_classes_XHTML_PackageParser($uploadedFile);
		$packageParser->validate();

		if(!$packageParser->isValid()){
			$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
			$this->setData('importErrors', $packageParser->getErrors());
			return $returnValue;
		}

		//extract the package
		$folder = $packageParser->extract();
		if(!is_dir($folder)){
			$this->setData('importErrorTitle', __('An error occured during the import'));
			$this->setData('importErrors', array(array('message' => __('unable to extract archive content, please check your tmp dir'))));
			return $returnValue;
		}

		//load and validate the manifest
//		$fileParser = new tao_models_classes_Parser($folder .'/index.html', array('extension' => 'html'));
//		$fileParser->validate(BASE_PATH.'/models/classes/data/xhtml/xhtml.xsd');
//		if(!$fileParser->isValid() && !$forceValid){
//			$this->setData('importErrorTitle', __('Validation of the imported file has failed'));
//			$this->setData('importErrors', $fileParser->getErrors());
//			return $returnValue;
//		}

		//confirm item model
		$rdfItem->editPropertyValues(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_XHTML);

		$itemContent = file_get_contents($folder .'/index.html');

		$itemPath = $this->service->getItemFolder($rdfItem);
		if(!tao_helpers_File::move($folder, $itemPath)){
			$this->setData('importErrorTitle', __('Unable to copy the resources'));
			$this->setData('importErrors', array(array('message' => __('Unable to move')." $folder to $itemPath")));
			return $returnValue;
		}

		$this->service->setItemContent($rdfItem, $itemContent, null, 'HOLD_COMMIT');
		$versionedFolder = $rdfItem->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_VERSIONED_CONTENT_PROPERTY));
		$versionedFolder = new core_kernel_versioning_File($versionedFolder->getUri());
		if(empty($commitMessage)) {
			$commitMessage = 'OWI package uploaded';
		}
		if($versionedFolder->add(true, true) && $versionedFolder->commit('[OWI pack] '.$commitMessage)){
			$returnValue = true;
		}

		$this->setData('message',__('item content successfully imported'));

		//remove the temp files
		tao_helpers_File::remove($uploadedFile);
		tao_helpers_File::remove(str_replace('.zip', '', $uploadedFile), true);

		return (bool) $returnValue;
	}

	/**
	 * Edit the row item content: download and upload the item content from the XML format
	 */
	public function itemContentIO()
	{

		$item = $this->getCurrentInstance();
		$itemClass = $this->getCurrentClass();

		//instantiate the item content form container
		$formContainer = new taoItems_actions_form_ItemContentIO($itemClass, $item);
		$myForm = $formContainer->getForm();

		if($myForm->isSubmited()){
			if($myForm->isValid()){

				$data = $myForm->getValues();

				if(isset($data['file_import']['uploaded_file'])){

					$extension = 'xml';

					//get the Xml Schema regarding the item model
					$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
					switch($itemModel->getUri()){
					 	case TAO_ITEM_MODEL_PAPERBASED:
							$validate = false;
					 		break;
					 	case TAO_ITEM_MODEL_QTI:
							$qtifolder = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQTI')->getConstant('BASE_PATH');
							$schema = $qtifolder . 'models/classes/QTI/data/imsqti_v2p0.xsd';
							$validate = true;
							break;
					 	case TAO_ITEM_MODEL_XHTML:
					 		$extension = 'html';
					 		$schema = BASE_PATH . '/models/classes/data/xhtml/xhtml.xsd';
							$validate = true;
					 		break;
					 	default:
					 		$modelName = strtolower(trim($itemModel->getLabel()));
					 		$schema = BASE_PATH . "/models/classes/data/{$modelName}/{$modelName}.xsd";
							$validate = true;
					 		break;

					}

					//parse and validate the sent file
					$parser = new tao_models_classes_Parser($data['file_import']['uploaded_file'], array('extension' => $extension));

					//check if the valdiation should be skipped
					if(isset($data['disable_validation'])){
						if(in_array('on', $data['disable_validation'])){
							$validate = false;
						}
					}
					if(!$validate){
						$parser->forceValidation();
					}

					if(!empty($schema)){
						//run the validation
						$parser->validate($schema);
					}

					if($parser->isValid()){
						//if the file is valid, we set it as the property of the item
						$this->service->setItemContent($item, file_get_contents($data['file_import']['uploaded_file']));
						$item->editPropertyValues(new core_kernel_classes_Property(TAO_ITEM_SOURCENAME_PROPERTY), $data['file_import']['name']);
						$formContainer->addDownloadSection();

						$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->getUri()));
						$this->setData('message', __('Item content saved'));

					}

					//get the errors (is empty if the file is valid)
					$this->setData('importErrors', $parser->getErrors());
				}
			}
		}
		$this->setData('uri', tao_helpers_Uri::encode($item->getUri()));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->getUri()));

		$this->setData('formTitle', __('Manage item content'));
		$this->setData('myForm', $myForm->render());

		$this->setView('form_content.tpl');
	}

	/**
	 * Display directly the content of the preview, outside any container
	 */
	public function fullScreenPreview(){
            $item = $this->getCurrentInstance();
            
            $options = array(
                    'uri' => $this->getRequestParameter('uri'),
                    'fullScreen' => true
                );
            
            $itemModel = $this->service->getItemModel($item);
            if($itemModel != null && $itemModel->getUri() == TAO_ITEM_MODEL_QTI){
                $this->redirect(_url('index', 'QtiPreview', 'taoQTI', $options));
            } else {
                $this->redirect(_url('index', 'ItemPreview', 'taoItems', $options));
            }
	}

	/**
	 * Get the Url with right options to run the preview
	 * @param core_kernel_classes_Resource $item
	 * @param core_kernel_classes_Class    $clazz
	 * @return string|null
	 */
	protected function getPreviewUrl(core_kernel_classes_Resource $item, core_kernel_classes_Class $clazz)
	{

		$previewUrl = null;

		if($this->service->hasItemContent($item) && $this->service->isItemModelDefined($item)){

			$options = array(
				'uri'		=>	tao_helpers_Uri::encode($item->getUri()),
				'classUri'	=> 	tao_helpers_Uri::encode($clazz->getUri()),
				'context'	=> false,
				'match'		=> 'server'
			);
			if($this->hasSessionAttribute('previewOpts')){
				$options = array_merge($options, $this->getSessionAttribute('previewOpts'));
			}

			$previewUrl =  _url('index', 'ItemPreview', 'taoItems', $options);
		}

		return $previewUrl;
	}



	/**
	 * Edit a class
	 */
	public function editItemClass()
	{
		$clazz = $this->getCurrentClass();

		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}

		$myForm = $this->editClass($clazz, $this->service->getRootClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->getUri()));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit item class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}

	/**
	 * Sub Class
	 * @return void
	 */
	public function addItemClass()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$subClass = $this->service->createSubClass($this->getCurrentClass());
		if($subClass instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $subClass->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($subClass->getUri())
			));
		}
	}

	/**
	 * delete an item or an item class
	 * called via ajax
	 * @see TaoModule::delete
	 * @return void
	 */
	public function delete()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}

		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteItem($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteItemClass($this->getCurrentClass());
		}
		echo json_encode(array('deleted'	=> $deleted));
	}

	/**
	 * @see TaoModule::translateInstance
	 * @return void
	 */
	public function translateInstance()
	{
		parent::translateInstance();
		$this->setView('form.tpl');
	}

	/**
	 * Display the Item.ItemContent property value.
	 * It's used by the authoring runtime/tools to retrieve the content
	 * @return void
	 */
	public function getItemContent()
	{

		$this->setContentHeader('text/xml');

		try{
			//output direclty the itemContent as XML
			print $this->service->getItemContent($this->getCurrentInstance());

		}
		catch(Exception $e){
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
	 */
	public function downloadItemContent()
	{

		$instance = $this->getCurrentInstance();
		if($this->service->isItemModelDefined($instance)){

        	$itemModel = $instance->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			$filename = $instance->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_SOURCENAME_PROPERTY));
        	if (is_null($filename)) {
				$filename = $itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY));
        	}

			$itemContent = $this->service->getItemContent($instance);
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
	 * @return void
	 */
	public function authoring()
	{

		$this->setData('error', false);

		try{
			$item = $this->getCurrentInstance();
			$itemClass = $this->getCurrentClass();

			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			if($itemModel instanceof core_kernel_classes_Resource){

				$authoring = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_AUTHORING_PROPERTY));

                if($authoring instanceof core_kernel_classes_Literal){

					$this->redirect(ROOT_URL.(string) $authoring.'?instance='.urlencode($item->getUri()).'&STANDALONE_MODE='.intval(tao_helpers_Context::check('STANDALONE_MODE')));

				}
			}
			$this->setData('instanceUri', tao_helpers_Uri::encode($item->getUri(), false));

		}
		catch(Exception $e){
			$this->setData('error', true);

                        //build clear error or warning message:
                        if(!empty($itemModel) && $itemModel instanceof core_kernel_classes_Resource){
                                $errorMsg = __('No item authoring tool available for the selected type of item: '.$itemModel->getLabel());
                        }else{
                                $errorMsg = __('No item type selected for the current item.')." {$item->getLabel()} ".__('Please select first the item type!');
                        }
                        $this->setData('errorMsg', $errorMsg);
		}
		$this->setData('uri', tao_helpers_Uri::encode($item->getUri()));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->getUri()));

		$this->setView('authoring.tpl');
	}

	/**
	 * use the xml content in session and set it to the item
	 * forwarded to the index action
	 * @return void
	 */
	public function saveItemContent()
	{

		$message = __('An error occured while saving the item');

		if(isset($_SESSION['instance']) && isset($_SESSION['xml'])){

			$item = new core_kernel_classes_Resource($_SESSION['instance']);
			if($this->service->isItemModelDefined($item)){

				$itemContentSaved = $this->service->setItemContent($item, $_SESSION['xml']);

				if(!$itemContentSaved){
					$message = __('Item saving failed');
				}else{
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->getUri()));
					$message = __('Item successfully saved');
				}
			}

			if(tao_helpers_Context::check('STANDALONE_MODE')){
				$itemClass = $this->service->getClass($item);
				$this->redirect(_url('authoring', 'SaSItems', 'taoItems', array('uri' => tao_helpers_Uri::encode($item->getUri()).'&classUri='.tao_helpers_Uri::encode($itemClass->getUri()), 'classUri' => tao_helpers_Uri::encode($itemClass->getUri()), 'message' => urlencode($message))));
			}
			else{
				$this->redirect(_url('index', 'Main', 'tao', array('message' => urlencode($message))));
			}
		}
	}

	/**
	 * Load an item external media
	 * It prevents to get it direclty in the data folder that access is denied
	 *
	 */
	public function getMediaResource()
	{
		
		if( $this->hasRequestParameter('path')){
			
			$item = null;
			if ($this->hasRequestParameter('uri') && $this->hasRequestParameter('classUri')){
				$item = $this->getCurrentInstance();
			}
			else if ($this->hasSessionAttribute('uri') && $this->hasSessionAttribute('classUri')){
				$classUri = tao_helpers_Uri::decode($this->getSessionAttribute('classUri'));
				if ($this->service->isItemClass(new core_kernel_classes_Class($classUri))){
					$item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getSessionAttribute('uri')));
				}
			}

			if (!is_null($item)){
				
				$path = urldecode($this->getRequestParameter('path'));
				if (!tao_helpers_File::securityCheck($path)){
					throw new Exception('Unauthorized path '.$path);
				}
				if (preg_match('/(.)+\/filemanager\/views\/data\//i', $path)){
					// check if the file is linked to the file manager
					$resource = preg_replace('/(.)+\/filemanager\/views\/data\//i', ROOT_PATH . '/filemanager/views/data/', $path);
				}else{
				    // look in the item's dedicated folder. it should be a resource
				    // that is local to the item, not it the file manager
				    // $folder is the item's dedicated folder path, $path the path to the resource, relative to $folder
					$folder 	= $this->service->getItemFolder($item);
					$resource 	= tao_helpers_File::concat(array($folder, $path));
				}

				if(file_exists($resource)){
					$mimeType = tao_helpers_File::getMimeType($resource);

					//allow only images, video, flash (and css?)
					if (preg_match("/^(image|video|audio|application\/x-shockwave-flash)/", $mimeType)){
						header("Content-Type: $mimeType; charset utf-8");
						print trim(file_get_contents($resource));
					}
				}
			}
		}
	}

	/**
	 * Authoring File mappgin service:
	 * Send into the request the parameters id and/or uri or nothing.
	 * Must be called via Ajax.
	 * Render json response {id: id, uri: uri}
	 * @return void
	 */
	public function getAuthoringFile()
	{

		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}

		$itemUri 	= $this->getRequestParameter('id');
		$uriParam 	= $this->getRequestParameter('uri');

		$authoringFileData = array();

		if(!$uriParam){
			$authoringFileData = $this->service->getItemFolder($itemUri).'/black.xml';

		}

		echo json_encode($authoringFileData);
	}

	/**
	 * get the  BLACK/HAWAI  temporary authoring file
	 * @return void
	 */
	public function loadTempAuthoringFile()
	{
		header("Content-Type: text/xml; charset utf-8");
		if($this->hasRequestParameter('instance')){
			$uri = tao_helpers_Uri::decode($this->getRequestParameter('instance'));
			$item = new core_kernel_classes_Resource($uri);
			$itemFolder = $this->service->getItemFolder($item);
			if(is_dir($itemFolder)){
				$tmpFile = $itemFolder.'/tmp_black.xml';
				if(file_exists($tmpFile)){
					echo file_get_contents($tmpFile);
					return;
				}
			}
		}
		//print an empty response
		echo '<?xml version="1.0" encoding="utf-8" ?>';
	}

	/**
	 * save the BLACK/HAWAI temporary authoring file
	 * @return void
	 */
	public function saveTempAuthoringFile()
	{
		if($this->hasRequestParameter('instance')){
			$uri = tao_helpers_Uri::decode($this->getRequestParameter('instance'));
            $xml = $this->getRequestParameter('xml');
			$item = new core_kernel_classes_Resource($uri);
			$itemFolder = $this->service->getItemFolder($item);
			if(!is_dir($itemFolder)){
				mkdir($itemFolder);
			}
			if(is_dir($itemFolder)){
				file_put_contents($itemFolder.'/tmp_black.xml', html_entity_decode($xml));

			}
		}
	}
}
?>