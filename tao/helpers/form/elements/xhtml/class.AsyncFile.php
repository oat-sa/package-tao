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
 * Short description of class tao_helpers_form_elements_xhtml_AsyncFile
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_elements_xhtml_AsyncFile
    extends tao_helpers_form_elements_AsyncFile
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method feed
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function feed()
    {
        
        common_Logger::t('Evaluating AsyncFile '.$this->getName(), array('TAO'));
        if (isset($_POST[$this->name])) {
        	$struct = @unserialize($_POST[$this->name]);
        	if($struct !== false){
        		$this->setValue($struct);
        	} else {
        		common_Logger::w('Could not unserialise AsyncFile field '.$this->getName(), array('TAO'));
        	}
        }
        
    }

    /**
     * Short description of method render
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function render()
    {
        $returnValue = (string) '';

        $widgetName = 'Uploader_'.md5($this->name);

        $returnValue .= "<label class='form_desc' for='{$this->name}'>". _dh($this->getDescription())."</label>";
		$returnValue .= "<input type='hidden' name='{$this->name}' id='{$this->name}' value='' />";
		$returnValue .= "<div id='{$widgetName}_container' class='form-elt-container file-uploader'>";

		//get the upload max size
		$fileSize = tao_helpers_Environment::getFileUploadLimit();

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
		$returnValue .= '<script type="text/javascript">
				require([\'jquery\',  \'ui/feedback\', \'ui/uploader\'], function($, feedback){
					 $("#' . $widgetName . '_container").uploader({
							 uploadUrl: "' . ROOT_URL . 'tao/File/upload",
							inputName: "'.$widgetName.'",
							autoUpload: "' . $auto . '"  ,
							showResetButton: "' . !$auto . '" ,
							showUploadButton: "' . !$auto . '" ,
							fileSelect  : function(files, done){
											var error = [],
												files = files.filter(_.isObject),// due to Chrome drag\'n\'drop issue
												givenLength = files.length,
												filters = "'.implode(',',$mimetypes).'".split(",").filter(function(e){return e.length});

												if (filters.length){

													files = _.filter(files, function(file){
														return !file.type || _.contains(filters, file.type);//IE9 doesnt detect type, so lets rely on server validation
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
							$("input[name=\'' . $this->getName() . '\']").val(result.data);
						}
					 })
			});
			</script>';
        $returnValue .= "</div>";

        return (string) $returnValue;
    }

    /**
     * Short description of method getEvaluatedValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function getEvaluatedValue()
    {
        
    	return $this->getRawValue();
        
    }

}