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

error_reporting(E_ALL);

/**
 * A specific Data Binder which binds data coming from a Generis Form Instance
 * the Generis persistent memory.
 *
 * If the target instance was not set, a new instance of the target class will
 * created to receive the data to be bound.
 *
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package tao
 * @subpackage models_classes_dataBinding
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A data binder focusing on binding a source of data to a generis instance
 *
 * @author Jerome Bogaerts <jerome@taotesting.com>
 */
require_once('tao/models/classes/dataBinding/class.GenerisInstanceDataBinder.php');

/* user defined includes */
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CB8-includes begin
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CB8-includes end

/* user defined constants */
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CB8-constants begin
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CB8-constants end

/**
 * A specific Data Binder which binds data coming from a Generis Form Instance
 * the Generis persistent memory.
 *
 * If the target instance was not set, a new instance of the target class will
 * created to receive the data to be bound.
 *
 * @access public
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package tao
 * @subpackage models_classes_dataBinding
 */
class tao_models_classes_dataBinding_GenerisFormDataBinder
    extends tao_models_classes_dataBinding_GenerisInstanceDataBinder
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Simply bind data from a Generis Instance Form to a specific generis class
     *
     * The array of the data to be bound must contain keys that are property
     * The repspective values can be either scalar or vector (array) values or
     * values.
     *
     * - If the element of the $data array is scalar, it is simply bound using
     * - If the element of the $data array is a vector, the property values are
     * with the values of the vector.
     * - If the element is an object, the binder will infer the best method to
     * it in the persistent memory, depending on its nature.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array data An array of values where keys are Property URIs and values are either scalar, vector or object values.
     * @return mixed
     */
    public function bind($data)
    {
        $returnValue = null;

        // section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CBA begin
        try {
        	$instance = parent::bind($data);
        	
        	// Take care of what the generic data binding did not.
			foreach ($data as $p => $d){
				$property = new core_kernel_classes_Property($p);
				
				if ($d instanceof tao_helpers_form_data_UploadFileDescription){
					$this->bindUploadFileDescription($property, $d);
				}
			}
        	
        	$returnValue = $instance;
        }
        catch (common_Exception $e){
        	$msg = "An error occured while binding property values to instance '': " . $e->getMessage();
        	$instanceUri = $instance->getUri();
        	throw new tao_models_classes_dataBinding_GenerisFormDataBindingException($msg);
        }
        // section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003CBA end

        return $returnValue;
    }

    /**
     * Binds an UploadFileDescription with the target instance.
     *
     * @access protected
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  Property property The property to bind the data.
     * @param  UploadFileDescription desc the upload file description.
     * @return void
     */
    protected function bindUploadFileDescription( core_kernel_classes_Property $property,  tao_helpers_form_data_UploadFileDescription $desc)
    {
        // section 127-0-1-1-4a948c58:13d11fbcd0f:-8000:0000000000003C39 begin
        $instance = $this->getTargetInstance();
        
        // Delete old files.
        foreach ($instance->getPropertyValues($property) as $oF){
        	$oldFile = new core_kernel_versioning_File($oF);
        	$oldFile->delete(true);
        }
        
        $name = $desc->getName();
        $size = $desc->getSize();
        
        if (!empty($name) && !empty($size)){
        	// Move the file at the right place.
        	$source = $desc->getTmpPath();
        	$repository = tao_models_classes_TaoService::singleton()->getUploadFileSource();
        	$file = $repository->spawnFile($source, $desc->getName());
        	tao_helpers_File::remove($source);
        	
        	$instance->setPropertyValue($property, $file->getUri());
        	
        	// Update the UploadFileDescription with the stored file.
        	$desc->setFile($file);
        }
        // section 127-0-1-1-4a948c58:13d11fbcd0f:-8000:0000000000003C39 end
    }

} /* end of class tao_models_classes_dataBinding_GenerisFormDataBinder */

?>