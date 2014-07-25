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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - tao/helpers/form/elements/xhtml/class.AsyncFile.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 02.01.2012, 11:12:09 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_elements_AsyncFile
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.AsyncFile.php');

/* user defined includes */
// section 127-0-1-1--79a39ca7:12824fe53d5:-8000:00000000000023D9-includes begin
// section 127-0-1-1--79a39ca7:12824fe53d5:-8000:00000000000023D9-includes end

/* user defined constants */
// section 127-0-1-1--79a39ca7:12824fe53d5:-8000:00000000000023D9-constants begin
// section 127-0-1-1--79a39ca7:12824fe53d5:-8000:00000000000023D9-constants end

/**
 * Short description of class tao_helpers_form_elements_xhtml_AsyncFile
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
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
        // section 127-0-1-1-3ba812e2:1284379704f:-8000:00000000000023F8 begin
        common_Logger::t('Evaluating AsyncFile '.$this->getName(), array('TAO'));
        if (isset($_POST[$this->name])) {
        	$struct = @unserialize($_POST[$this->name]);
        	if($struct !== false){
        		$this->setValue($struct);
        	} else {
        		common_Logger::w('Could not unserialise AsyncFile field '.$this->getName(), array('TAO'));
        	}
        }
        // section 127-0-1-1-3ba812e2:1284379704f:-8000:00000000000023F8 end
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

        // section 127-0-1-1--79a39ca7:12824fe53d5:-8000:00000000000023DE begin

        $widgetName = 'AsyncFileUploader_'.md5($this->name);

        $returnValue .= "<label class='form_desc' for='{$this->name}'>". _dh($this->getDescription())."</label>";

		$returnValue .= "<div id='{$widgetName}_container' class='form-elt-container file-uploader'>";
        $returnValue .= "<input type='hidden' name='{$this->name}' id='{$this->name}' value='' />";
        $returnValue .= "<input type='file' name='{$widgetName}' id='{$widgetName}' ";
		$returnValue .= $this->renderAttributes();
		$returnValue .= "/>";
		$returnValue .= "<span>";
		$returnValue .= "<img src='".TAOBASE_WWW."img/file_upload.png' class='icon' />";
		$returnValue .= "<a href='#' id='{$widgetName}_starter' >".__('Start upload')."</a>";
		$returnValue .= "</span>";

		//get the upload max size
		$fileSize = tao_helpers_Environment::getFileUploadLimit();

		$extensions = array();

		//add a client validation
		foreach($this->validators as $validator){
			//get the valid file extensions
			if($validator instanceof tao_helpers_form_validators_FileMimeType){
				$options = $validator->getOptions();
				if(isset($options['extension'])){
					foreach($options['extension'] as $extension){
						$extensions[] = '*.'.$extension;
					}
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

		//initialize the AsyncFileUpload Js component
		$id = md5($this->name);
		$returnValue .= '<script type="text/javascript">
			$(document).ready(function(){
				require([\'require\', \'jquery\', \'AsyncFileUpload\'], function(req, $){
					myUploader_'.$id.' = new AsyncFileUpload("#'.$widgetName.'", {
						"scriptData": {"session_id": "'.session_id().'"},
						"basePath": "'.TAOBASE_WWW.'",
						"sizeLimit": '.$fileSize.',';
		if (count($extensions) > 0) {
 			$returnValue .='"fileDesc": "'.__('Allowed files types: ').implode(', ', $extensions).'", "fileExt": "'.implode(';', $extensions).'",';
		}
		$returnValue .='
						"starter" : "#'.$widgetName.'_starter",
						"target": "#'.$widgetName.'_container input[id=\''.$this->name.'\']",
						"submiter": ".form-submiter",
						"auto": '.$auto.',
						"folder": "/"
					});
				});
			});
			</script>';
        $returnValue .= "</div>";

        // section 127-0-1-1--79a39ca7:12824fe53d5:-8000:00000000000023DE end

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
        // section 127-0-1-1--19ea91f3:1349db91b83:-8000:00000000000034A0 begin
    	return $this->getRawValue();
        // section 127-0-1-1--19ea91f3:1349db91b83:-8000:00000000000034A0 end
    }

} /* end of class tao_helpers_form_elements_xhtml_AsyncFile */

?>