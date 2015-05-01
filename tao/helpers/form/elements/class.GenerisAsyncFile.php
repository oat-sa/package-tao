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
 * Short description of class tao_helpers_form_elements_GenerisAsyncFile
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
abstract class tao_helpers_form_elements_GenerisAsyncFile
    extends tao_helpers_form_FormElement
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute widget
     *
     * @access public
     * @var string
     */
    public $widget = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#GenerisAsyncFile';

    // --- OPERATIONS ---

    /**
     * Short description of method setValue
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  value
     * @return mixed
     */
    public function setValue($value)
    {
        
    	if ($value instanceof tao_helpers_form_data_UploadFileDescription){
    		// The file is being uploaded.
    		$this->value = $value;
    	}
    	else if (common_Utils::isUri($value)){
    		// The file has already been uploaded
    		$file = new core_kernel_file_File($value);
    		$this->value = new tao_helpers_form_data_StoredFileDescription($file);
    	}
    	else{
    		// Empty file upload description, nothing was uploaded.
    		$this->value = new tao_helpers_form_data_UploadFileDescription('', 0, '', '');
    	}
        
    }

}

?>