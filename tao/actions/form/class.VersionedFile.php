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
 * Short description of class tao_actions_form_VersionedFile
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 
 */
class tao_actions_form_VersionedFile
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute ownerInstance
     *
     * @access protected
     * @var Resource
     */
    protected $ownerInstance = null;

    /**
     * Short description of attribute property
     *
     * @access protected
     * @var Property
     */
    protected $property = null;

    /**
     * Short description of attribute versionedFile
     *
     * @access public
     * @var File
     */
    public $versionedFile = null;

    // --- OPERATIONS ---

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        
        
		if(!isset($this->options['instanceUri'])){
    		throw new Exception(__('Option instanceUri is not an option !!'));
    	}
    	if(!isset($this->options['ownerUri'])){
    		throw new Exception(__('Option ownerUri is not an option !!'));
    	}
    	if(!isset($this->options['propertyUri'])){
    		throw new Exception(__('Option propertyUri is not an option !!'));
    	}
    	
    	$this->ownerInstance = new core_kernel_classes_Resource($this->options['ownerUri']);
    	$this->property = new core_kernel_classes_Property($this->options['propertyUri']);
    	$this->versionedFile = new core_kernel_versioning_File($this->options['instanceUri']);
		
    	$this->form = tao_helpers_form_FormFactory::getForm('versioned_file');
    	
		$actions = tao_helpers_form_FormFactory::getElement('save', 'Free');
		$value = '';
		$value .=  '<a href="#" class="form-submitter btn-success small"><span class="icon-save"></span>' .__('Save').'</a>';
        
		$actions->setValue($value);
		
    	$this->form->setActions(array($actions), 'top');
    	$this->form->setActions(array($actions), 'bottom');
    	
        
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        
    	
    	$versioned = $this->versionedFile->isVersioned();
		$freeFilePath = isset($this->options['freeFilePath'])?(bool)$this->options['freeFilePath']:false;
    	
		/*
		 * 1. BUILD FORM
		 */
    	
		// File Content
    	$contentGroup = array();
    	function return_bytes($val) {
			$val = trim($val);
			$last = strtolower($val[strlen($val)-1]);
			switch($last) {
				// Le modifieur 'G' est disponible depuis PHP 5.1.0
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}

			return $val;
		}
		$browseElt = tao_helpers_form_FormFactory::getElement("file_import", "AsyncFile");
		$browseElt->addValidator(tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => return_bytes(ini_get('post_max_size')))));
    	//make the content compulsory if it does not exist already
		if(!$versioned){
			$browseElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
			$browseElt->setDescription(__("Upload the file to version"));
		}
		else{
			$browseElt->setDescription(__("Upload a new content"));
		}
		$this->form->addElement($browseElt);
		array_push($contentGroup, $browseElt->getName());
    	
		$commitMessageElt = tao_helpers_form_FormFactory::getElement("commit_message", "Textarea");
		$commitMessageElt->setDescription(__("Commit message : "));
		$this->form->addElement($commitMessageElt);
		array_push($contentGroup, $commitMessageElt->getName());
		
		// if the file is already versioned add a way to download it
		if($versioned){
			
			$downloadUrl = $this->getDownloadUrl($this->versionedFile);
			
			$downloadFileElt = tao_helpers_form_FormFactory::getElement("file_download", 'Free');
			$downloadFileElt->setValue("<a href='$downloadUrl' class='blink' target='_blank'><img src='".TAOBASE_WWW."img/document-save.png' alt='Download versioned file' class='icon'  /> ".__('Download content')."</a>");
			$this->form->addElement($downloadFileElt);
			array_push($contentGroup, $downloadFileElt->getName());
			
			$deleteFileElt0 = tao_helpers_form_FormFactory::getElement("file_delete0", 'Free');
			$deleteFileElt0->setValue("<a id='delete-versioned-file' href='#' class='blink' target='_blank'><img src='".TAOBASE_WWW."img/edit-delete.png' alt='Delete versioned file' class='icon'  /> ".__('Remove content')."</a>");
			$this->form->addElement($deleteFileElt0);
			array_push($contentGroup, $deleteFileElt0->getName());
			
			$deleteFileElt = tao_helpers_form_FormFactory::getElement("file_delete", 'Hidden');
			$deleteFileElt->setValue(0);
			$this->form->addElement($deleteFileElt);
			array_push($contentGroup, $deleteFileElt->getName());
		}
		
		$this->form->createGroup('file', 'Content', $contentGroup);
		
    	//File Meta
    	$fileNameElt = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode(PROPERTY_FILE_FILENAME), $versioned ? 'Label' : 'Textbox');
		$fileNameElt->setDescription(__("File name"));
		if(!$versioned){ 
			$fileNameElt->addValidator(tao_helpers_form_FormFactory::getValidator('FileName'));
		}
		$this->form->addElement($fileNameElt);
		
		//file path element to be added or not:
		$filePathElt = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode(PROPERTY_FILE_FILEPATH), $freeFilePath?'Textbox':'Hidden');
		$filePathElt->setDescription(__("File path"));
		$this->form->addElement($filePathElt);
		
		$repositoryEltOptions = array();
		foreach(helpers_FileSource::getFileSources() as $fileSystem){
			$repositoryEltOptions[tao_helpers_Uri::encode($fileSystem->getUri())] = $fileSystem->getLabel();
		}
		$fileRepositoryElt = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode(PROPERTY_FILE_FILESYSTEM), $versioned ? 'Label' : 'Radiobox');
		$fileRepositoryElt->setDescription(__("File repository"));
		if(!$versioned){
			$fileRepositoryElt->setOptions($repositoryEltOptions);
		}
		$fileRepositoryElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$this->form->addElement($fileRepositoryElt);
    	
    	$this->form->createGroup('meta', 'Description', array(
			$fileNameElt->getName(),
			$filePathElt->getName(),
			$fileRepositoryElt->getName()
		));
    	
		// File Revision
		if($versioned){
			$fileVersionOptions = array();
			$history = $this->versionedFile->gethistory();
			$countHistory = count($history);
			foreach($history as $i => $revision){
				$date = new DateTime($revision['date']);
				$fileVersionOptions[$countHistory-$i] = $countHistory-$i . '. ' . $revision['msg'] . ' [' . $revision['author'] .' / ' . $date->format('Y-m-d H:i:s') . '] ';
			}
			
			$fileRevisionElt = tao_helpers_form_FormFactory::getElement('file_version', 'Radiobox');
			$fileRevisionElt->setDescription(__("File revision"));
			$fileRevisionElt->setOptions($fileVersionOptions);
			$fileRevisionElt->setValue($countHistory);
			$this->form->addElement($fileRevisionElt);
			$this->form->createGroup('revision', 'Version', array($fileRevisionElt->getName()));
		}
		
		/*
		 * 2. HIDDEN FIELDS
		 */
		//add an hidden elt for the property uri (Property associated to the owner instance)
		$propertyUriElt = tao_helpers_form_FormFactory::getElement("propertyUri", "Hidden");
		$propertyUriElt->setValue(tao_helpers_Uri::encode($this->property->getUri()));
		$this->form->addElement($propertyUriElt);
		
		//add an hidden elt for the instance Uri
		//usefull to render the revert action
		$instanceUriElt = tao_helpers_form_FormFactory::getElement('uri', 'Hidden');
		$instanceUriElt->setValue(tao_helpers_Uri::encode($this->ownerInstance->getUri()));
		$this->form->addElement($instanceUriElt);
		
		/*
		 * 3. FILL THE FORM
		 */
    	if($versioned){
    		
			$fileNameValue = $this->versionedFile->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILENAME));
			if(!empty($fileNameValue)){
				$fileNameElt->setValue((string) $fileNameValue);
			}
		
			$filePathValue = $this->versionedFile->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH));
			if(!empty($filePathValue)){
				$filePathElt->setValue((string) $filePathValue);
			}
		
			$repositoryValue = $this->versionedFile->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILESYSTEM));
			if(!empty($repositoryValue)){
				$fileRepositoryElt->setValue($repositoryValue->getUri());
			}
			
			$history = $this->versionedFile->gethistory();
			$versionElt = $this->form->getElement('file_version');
			$versionElt->setValue(count($history));
			
    	}else{
			
			if(!$freeFilePath){
				$filePathElt->setValue($this->getDefaultFilePath());
			}else{
				$filePathElt->setValue('/');
			}
			
			$defaultRepo = $this->getDefaultRepository();
			if(!is_null($defaultRepo)){
				$fileRepositoryElt->setValue($defaultRepo->getUri());
			}
		}
    	
        
    }

    /**
     * Override the validate method of the form container to validate 
     * linked elements
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    public function validate()
    {
        $returnValue = (bool) false;

        
        
    	if($this->form->isSubmited()){
			
    		if($this->versionedFile->isVersioned()){
    			return true;
    		}
			
	    	$fileNameElt = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_FILE_FILENAME));
	    	$fileName = !is_null($fileNameElt)?$fileNameElt->getValue():'';
	    	
	    	$filePathElt = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_FILE_FILEPATH));
	    	$filePath = $filePathElt->getValue();
	    	
	    	$fileRepositoryElt = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_FILE_FILESYSTEM));
	    	$fileRepository = tao_helpers_Uri::decode($fileRepositoryElt->getValue());
	    	
	    	 //check if a resource with the same path exists yet in the repository
	        $clazz = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
	        $options = array('like' => false, 'recursive' => true);
			$propertyFilter = array(
				PROPERTY_FILE_FILENAME => $fileName,
				PROPERTY_FILE_FILEPATH => $filePath,
				PROPERTY_FILE_FILESYSTEM => $fileRepository
			);
	        $sameNameFiles = $clazz->searchInstances($propertyFilter, $options);
	        if(!empty($sameNameFiles)){
	        	$sameFileResource = array_pop($sameNameFiles);
	        	$sameFile = new core_kernel_versioning_File($sameFileResource->getUri());
	        	
	        	$this->form->valid = false;
	        	$this->form->error = __('A similar resource has already been versioned').' ('.$sameFile->getAbsolutePath().')';
	        }
    	}
    	
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getDownloadUrl
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function getDownloadUrl()
    {
        $returnValue = (string) '';

        
		
		if(!is_null($this->ownerInstance)){
			$returnValue = _url('downloadFile', 'File', 'tao', array('uri' => tao_helpers_Uri::encode($this->ownerInstance->getUri())));
		}
		
        

        return (string) $returnValue;
    }

    /**
     * Short description of method getDefaultFilePath
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function getDefaultFilePath()
    {
        $returnValue = (string) '';

        
		
		$returnValue = tao_helpers_Uri::getUniqueId($this->ownerInstance->getUri()).'/'.tao_helpers_Uri::getUniqueId($this->property->getUri());
			
        

        return (string) $returnValue;
    }

    /**
     * Short description of method getDefaultRepository
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return core_kernel_versioning_Repository
     */
    public function getDefaultRepository()
    {
        $returnValue = null;

        
        

        return $returnValue;
    }

}

?>