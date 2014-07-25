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

/**
 * Backward compatibility class
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 * @deprecated
 */
class core_kernel_classes_File
    extends core_kernel_file_File
{
    /**
     * Create an instance of class File from filename and filepath (filepath
     * be optionnal)
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string fileName
     * @param  string filePath
     * @param  string uri
     * @param  array options
     * @return core_kernel_classes_File
     * @deprecated
     */
    public static function create($fileName, $filePath = null, $uri = "", $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:000000000000136C begin
        
        // Default file path if not defined
      	if(is_null($filePath)){
            $filePath = GENERIS_FILES_PATH; 
        }

	    $instance = static::getFileClass()->createInstance('File : ' . $fileName, 'File : ' . $filePath. $fileName, $uri);
	    $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $instance->setPropertyValue($filePathProp, $filePath);
	    $instance->setPropertyValue($fileNameProp, $fileName);
	    
	    $returnValue = new core_kernel_classes_File($instance->getUri());
        
        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:000000000000136C end

        return $returnValue;
    }
}

?>