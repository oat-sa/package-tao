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
 * generis representation of a file
 *
 * @access public
 * @author Joel bout, <joel@taotesting.com>
 * @package core
 * @subpackage kernel_file
 */
class core_kernel_file_File
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getFileClass
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Class
     */
    protected static function getFileClass()
    {
        $returnValue = null;

        // section 10-30-1--78--1698032:13afe62e559:-8000:00000000000030B4 begin
        $returnValue = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
        // section 10-30-1--78--1698032:13afe62e559:-8000:00000000000030B4 end

        return $returnValue;
    }

    /**
     * Short description of method isFile
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public static function isFile( core_kernel_classes_Resource $resource)
    {
        return $resource->hasType(new core_kernel_classes_Class(CLASS_GENERIS_FILE));
    }

    /**
     * Get the absolute path to the directory where the file is stored.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string A path.
     */
    public function getAbsolutePath()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001367 begin
	    $props = $this->getPropertiesValues(array(
	    	new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH),
	    	new core_kernel_classes_Property(PROPERTY_FILE_FILENAME),
	    	new core_kernel_classes_Property(PROPERTY_FILE_FILESYSTEM)
	    ));
	    if (!isset($props[PROPERTY_FILE_FILEPATH]) || count($props[PROPERTY_FILE_FILEPATH]) == 0) {
	    	throw new common_Exception('filepath missing for file '.$this->getUri());
	    }
	    if (!isset($props[PROPERTY_FILE_FILENAME]) || count($props[PROPERTY_FILE_FILENAME]) == 0) {
	    	throw new common_Exception('filename missing for file '.$this->getUri());
	    }
	    if (!isset($props[PROPERTY_FILE_FILESYSTEM]) || count($props[PROPERTY_FILE_FILESYSTEM]) == 0) {
	    	throw new common_Exception('filesource missing for file '.$this->getUri());
	    }
	    $relFilePath = (string)current($props[PROPERTY_FILE_FILEPATH]);
	    $fileName	= (string)current($props[PROPERTY_FILE_FILENAME]);
        $fileSystem	= new core_kernel_fileSystem_FileSystem(current($props[PROPERTY_FILE_FILESYSTEM]));
         
	    $path = $fileSystem->getPath();
	    if (!empty($relFilePath)) {
	    	$path .= $relFilePath.DIRECTORY_SEPARATOR;
	    }
	    
        if(empty($fileName)) {
	        //IF the resource is a folder resource, the absolute filepath should respect a specific format without slash as last char
        	$returnValue = rtrim($path, DIRECTORY_SEPARATOR);
        } else {
	        $returnValue = $path . $fileName;
        }
        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001367 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getFileContent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function getFileContent()
    {
        // section 127-0-1-1--77b1997d:12bf34c2951:-8000:0000000000001386 begin
        if (!file_exists($this->getAbsolutePath())){
        	throw new common_exception_FileSystemError(__('File not found '.$this->getAbsolutePath()));
        }
    	return @file_get_contents($this->getAbsolutePath());
        // section 127-0-1-1--77b1997d:12bf34c2951:-8000:0000000000001386 end
    }
    
    /**
     * Returns the filesystem this file is associated to 
     * 
     * @return core_kernel_fileSystem_FileSystem
     */
    public function getFileSystem()
    {
        $fs = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILESYSTEM));
        return new core_kernel_fileSystem_FileSystem($fs);
    }

    /**
     * Returns the relativ path to this file
     *
     * @return string
     */
    public function getRelativePath()
    {
        return $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH));
    }
    
    /**
     * Short description of method delete
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete($deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001672 begin
        /*if(file_exists($this->getAbsolutePath())){
        	if (!@unlink($this->getAbsolutePath())){
        		throw new Exception(__('Unable to remove the file '.$this->getAbsolutePath()));
        	}
        }*/
        parent::delete($deleteReference);
        $returnValue = true;
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001672 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getFileInfo
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function getFileInfo()
    {
        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001671 begin
    	return new SplFileInfo($this->getAbsolutePath());
        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001671 end
    }

    /**
     * Short description of method setContent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string content
     * @return boolean
     */
    public function setContent($content)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001675 begin
        
        $filePath = $this->getAbsolutePath();
        $path = explode(DIRECTORY_SEPARATOR, dirname($filePath));
        $breadCrumb = '';
        foreach($path as $bread){
        	$breadCrumb .= $bread.DIRECTORY_SEPARATOR;
        	if(!file_exists($breadCrumb)){
        		mkdir($breadCrumb);
        	}
        }
        
        if(file_put_contents($filePath, $content)===false){
            $returnValue = false;
        }else{
            $returnValue = true;
        }
        
        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001675 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method fileExists
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function fileExists()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016EB begin
        $returnValue = file_exists($this->getAbsolutePath());        
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016EB end

        return (bool) $returnValue;
    }

}

?>