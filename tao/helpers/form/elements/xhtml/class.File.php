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
 * TAO - tao/helpers/form/elements/xhtml/class.File.php
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
 * include tao_helpers_form_elements_File
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.File.php');

/* user defined includes */
// section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CC9-includes begin
// section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CC9-includes end

/* user defined constants */
// section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CC9-constants begin
// section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CC9-constants end

/**
 * Short description of class tao_helpers_form_elements_xhtml_File
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */
class tao_helpers_form_elements_xhtml_File
    extends tao_helpers_form_elements_File
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
        // section 127-0-1-1-7109ddcd:1344660e25c:-8000:0000000000003482 begin
		if(isset($_FILES[$this->getName()])){
			$this->setValue($_FILES[$this->getName()]);
		}else{
			throw new tao_helpers_form_Exception('cannot evaluate the element '.__CLASS__);
		}
        // section 127-0-1-1-7109ddcd:1344660e25c:-8000:0000000000003482 end
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

        // section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CCA begin
		
		if(!empty($this->value)){
			if(common_Utils::isUri($this->value)){
				$file = new core_kernel_file_File($this->value);
				if($file->fileExists()){
					$fileInfo = $file->getFileInfo();
					$fileInfo->getFilename();
				}else{
					$file->delete();
				}
			}
		}
		
		$returnValue .= "<label class='form_desc' for='{$this->name}'>". _dh($this->getDescription())."</label>";
		$returnValue .= "<input type='hidden' name='MAX_FILE_SIZE' value='".tao_helpers_form_elements_File::MAX_FILE_SIZE."' />";
		$returnValue .= "<input type='file' name='{$this->name}' id='{$this->name}' ";
		$returnValue .= $this->renderAttributes();
		$returnValue .= " value='{$this->value}'  />";
		
        // section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CCA end

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
        // section 127-0-1-1--19ea91f3:1349db91b83:-8000:00000000000034A2 begin
        return $this->getRawValue();
        // section 127-0-1-1--19ea91f3:1349db91b83:-8000:00000000000034A2 end
    }

} /* end of class tao_helpers_form_elements_xhtml_File */

?>