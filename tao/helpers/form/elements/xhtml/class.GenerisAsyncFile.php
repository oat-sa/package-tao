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
 * Short description of class tao_helpers_form_elements_xhtml_GenerisAsyncFile
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
class tao_helpers_form_elements_xhtml_GenerisAsyncFile
    extends tao_helpers_form_elements_GenerisAsyncFile
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method feed
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return mixed
     */
    public function feed()
    {
        
    	if (isset($_POST[$this->name])) {
    		$struct = @unserialize($_POST[$this->name]);
    		if($struct !== false){
    			$desc = new tao_helpers_form_data_UploadFileDescription(	$struct['name'],
    					$struct['size'],
    					$struct['type'],
    					$struct['uploaded_file']);
    			$this->setValue($desc);
    		}
    		else{
    			// else, no file was selected by the end user.
    			// set the value as empty in order
    			$this->setValue($_POST[$this->name]);
    		}
    	}
        
    }

    /**
     * Short description of method render
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function render()
    {
        $returnValue = (string) '';

        
        $widgetName = $this->buildWidgetName();
        $widgetContainerId = $this->buildWidgetContainerId();
        
        $returnValue .= "<label class='form_desc' for='{$this->name}'>". _dh($this->getDescription())."</label>";
        $returnValue .= "<div id='${widgetContainerId}' class='form-elt-container file-uploader'>";
        
        if ($this->value instanceof tao_helpers_form_data_FileDescription && ($file = $this->value->getFile()) != null){
        	 
        	// A file is stored or has just been uploaded.
        	$shownFileName = $this->value->getName();
        	$shownFileSize = $this->value->getSize();
        	$shownFileSize = number_format($shownFileSize / 1000, 2); // to kb.
        	$shownFileTxt = sprintf(__('%s (%s kb)'), $shownFileName, $shownFileSize);
        	$deleteButtonTitle = __("Delete");
        	$deleteButtonId = $this->buildDeleteButtonId();
        	$downloadButtonTitle = __("Download");
        	$downloadButtonId = $this->buildDownloadButtonId();
        	$iFrameId = $this->buildIframeId();
        	$returnValue .= "<span class=\"widget_AsyncFile_fileinfo\">${shownFileTxt}</span>";
        	$returnValue .= "<button id=\"${downloadButtonId}\" type=\"button\" class=\"download btn-neutral small icon-download\" title=\"${downloadButtonTitle}\">";
        	$returnValue .= "<button id=\"${deleteButtonId}\" type=\"button\" class=\"delete btn-error small icon-bin\" title=\"${deleteButtonTitle}\"/>";
        	$returnValue .= "<iframe style=\"display:none\" id=\"${iFrameId}\" frameborder=\"0\"/>";

        	// Inject behaviour of the Delete/Download buttons component in response.
        	$returnValue .= self::embedBehaviour($this->buildDeleterBehaviour() . $this->buildDownloaderBehaviour());
        }
        else{
        	 
        	// No file stored yet.
        	// Inject behaviour of the AsyncFileUpload component in response.
        	$returnValue .= self::embedBehaviour($this->buildUploaderBehaviour());
        }
        
        
        $returnValue .= "</div>";
        

        return (string) $returnValue;
    }

    /**
     * Short description of method getEvaluatedValue
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return mixed
     */
    public function getEvaluatedValue()
    {
        
    	return $this->getRawValue();
        
    }

    /**
     * Short description of method buildDeleterBehaviour
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function buildDeleterBehaviour()
    {
        $returnValue = (string) '';

        
        $deleteButtonId = $this->buildDeleteButtonId();
         
        $returnValue .= '$(document).ready(function() {';
        $returnValue .= '	$("#' . $deleteButtonId . '").click(function() {';
        $returnValue .= '		$("#' . $this->buildWidgetContainerId() . '").empty();';
        $returnValue .= '		' . $this->buildUploaderBehaviour(true);
        $returnValue .= '	});';
        $returnValue .= '});';
        

        return (string) $returnValue;
    }

    /**
     * Short description of method buildUploaderBehaviour
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  boolean deleted
     * @return string
     */
    public function buildUploaderBehaviour($deleted = false)
    {
        $returnValue = (string) '';

        
        $widgetName = $this->buildWidgetName();
         
        //get the upload max size (the min of those 3 directives)
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        $fileSize = min($max_upload, $max_post, $memory_limit) * 1024 * 1024;

        $mimetypes = array();
        
        //add a client validation
        foreach($this->validators as $validator){
        	//get the valid file extensions
        	if($validator instanceof tao_helpers_form_validators_FileMimeType){
        		$options = $validator->getOptions();
                if(isset($options['mimetype'])){
                    $mimetypes = $options['mimetype'] ;
                }
        	}
        	//get the max file size
        	if($validator instanceof tao_helpers_form_validators_FileSize){
        		$options = $validator->getOptions();
        		if(isset($options['max'])){
        			$validatorMax = (int)$options['max'];
        			if($validatorMax > 0 && $validatorMax < $fileSize){
        				$fileSize = $validatorMax;
        			}
        		}
        	}
        }
        
        //default value for 'auto' is 'true':
        $auto = 'true';
        if(isset($this->attributes['auto'])){
        	if(!$this->attributes['auto'] || $this->attributes['auto'] === 'false') {
        	    $auto = 'false';
        	}
        	unset($this->attributes['auto']);
        }
        
        //initialize the Uploader Js component
        $returnValue .= '
                 require([\'jquery\',  \'ui/feedback\', \'ui/uploader\'], function($, feedback){
					 $("#' . $this->buildWidgetContainerId().'").uploader({
							 uploadUrl: "' . ROOT_URL . 'tao/File/upload",
							inputName: "'.$widgetName.'",
							autoUpload: "' . $auto . '"  ,
							showResetButton: "' . !$auto . '" ,
							showUploadButton: "' . !$auto . '" ,
							fileSelect  : function(files, done){
											var error = [],
												givenLength = files.length,
												filters = "'.implode(',',$mimetypes).'".split(",").filter(function(e){return e.length});

												if (filters.length){

													files = _.filter(files, function(file){
														return _.contains(filters, file.type);
													});

													if(files.length !== givenLength){
														error.push( "Unauthorized files have been removed");
													}

												}

												files = _.filter(files, function(file){
														return file.size <= ' . $fileSize . ';
													});

												if(files.length !== givenLength && !error.length){
													error.push( "Size limit is ' . $fileSize . ' bytes");
												}

												if (error.length){
													feedback().error(error.join(","));
												}

												done(files);
												if ( '.$auto.' ){
													$(this).uploader("upload");
												}
										 }

					 }).on("upload.uploader", function(e, file, result){
					 	if ( result && result.uploaded ){
							$(e.target).append($("<input type=\'hidden\' name=\'' . $this->getName() . '\'/>").val(result.data));
						}
					 })
			});';


        return (string) $returnValue;
    }

    /**
     * Short description of method buildWidgetName
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function buildWidgetName()
    {
        $returnValue = (string) '';

        
        $returnValue = 'AsyncFileUploader_'.md5($this->name);
        

        return (string) $returnValue;
    }

    /**
     * Short description of method buildDeleteButtonId
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function buildDeleteButtonId()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->buildWidgetName() . '_deleter';
        

        return (string) $returnValue;
    }

    /**
     * Short description of method buildWidgetContainerId
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function buildWidgetContainerId()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->buildWidgetName() . '_container';
        

        return (string) $returnValue;
    }

    /**
     * Short description of method embedBehaviour
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string behaviour
     * @return string
     */
    public function embedBehaviour($behaviour)
    {
        $returnValue = (string) '';

        
        $returnValue = '<script type="text/javascript">' . $behaviour . '</script>';
        

        return (string) $returnValue;
    }

    /**
     * Short description of method buildDownloadButtonId
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function buildDownloadButtonId()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->buildWidgetName() . '_downloader';
        

        return (string) $returnValue;
    }

    /**
     * Short description of method buildDownloaderBehaviour
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function buildDownloaderBehaviour()
    {
        $returnValue = (string) '';

        
        $downloadButtonId = $this->buildDownloadButtonId();
        $iFrameId = $this->buildIframeId();
        $fileUri = $this->value->getFile()->getUri();
        $fileUri = tao_helpers_Uri::encode($fileUri);
         
        $returnValue .= '$(document).ready(function() {';
        $returnValue .= '	$("#' . $downloadButtonId . '").click(function() {';
        $returnValue .= '		$("#' . $iFrameId . '").attr("src", "'.ROOT_URL.'tao/File/downloadFile?uri=' . $fileUri . '")';
        $returnValue .= '	});';
        $returnValue .= '});';
        

        return (string) $returnValue;
    }

    /**
     * Short description of method buildIframeId
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function buildIframeId()
    {
        $returnValue = (string) '';

        
        $returnValue = $this->buildWidgetName() . '_iframe';
        

        return (string) $returnValue;
    }

}
