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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

/**
 * Short description of class core_kernel_versioning_Repository
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package generis
 
 */
class core_kernel_versioning_Repository
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute authenticated
     *
     * @access private
     * @var boolean
     */
    private $authenticated = false;

    // --- OPERATIONS ---

    /**
     * Repository factory
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource type
     * @param  string url
     * @param  string login
     * @param  string password
     * @param  string path
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_versioning_Repository
     * @deprecated
     */
    public static function create( core_kernel_classes_Resource $type, $url, $login, $password, $path, $label, $comment, $uri = '')
    {
        $returnValue = null;

        
        $returnValue = core_kernel_fileSystem_FileSystemFactory::createFileSystem($type, $url, $login, $password, $path, $label);
        

        return $returnValue;
    }

    /**
     * Checkout the remote repository to a local directory
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  int revision
     * @return boolean
     */
    public function checkout($revision = null)
    {
        $returnValue = (bool) false;

        
        $VersioningRepositoryUrlProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL);
		$url = (string)$this->getOnePropertyValue($VersioningRepositoryUrlProp);
		
		$VersioningRepositoryPathProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH);
		$path = (string)$this->getOnePropertyValue($VersioningRepositoryPathProp);
		
//        if ($this->authenticate()){
        	$returnValue = core_kernel_versioning_RepositoryProxy::singleton()->checkout($this, $url, $path, $revision);
//        }
        
        

        return (bool) $returnValue;
    }

    /**
     * Get the repository type
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return core_kernel_classes_Resource
     */
    public function getVCSType()
    {
        $returnValue = null;

        
        $returnValue = $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE));
        

        return $returnValue;
    }

    /**
     * Get path of the local repository
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function getPath()
    {
        $returnValue = (string) '';

        
        $returnValue = core_kernel_fileSystem_Cache::getFileSystemPath($this);
        

        return (string) $returnValue;
    }

    /**
     * Get authenticated with the remote repository
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return boolean
     */
    public function authenticate()
    {
        $returnValue = (bool) false;

        
          
        if($this->authenticated){
        	
        	$returnValue = true;
        } else {
        	
        	
	        $VersioningRepositoryLoginProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN);
			$login = (string) $this->getOnePropertyValue($VersioningRepositoryLoginProp);
			
			$VersioningRepositoryPasswordProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD);
			$password = (string) $this->getOnePropertyValue($VersioningRepositoryPasswordProp); 
			
			$returnValue = $this->authenticated = core_kernel_versioning_RepositoryProxy::singleton()->authenticate($this, $login, $password);

        }
		
        

        return (bool) $returnValue;
    }

    /**
     * Delete the repository.
     * Be carrefull, the function does not delete the directory in the file 
     * system.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete($deleteReference = false)
    {
        $returnValue = (bool) false;
        
        /* remove the resource implies other consequence, do not remove 
        $path = $this->getPath();
        if(is_dir($path)){
        	// Remove the local copy
        	helpers_File::remove($path);
        }*/
        
        $returnValue = parent::delete();
		core_kernel_fileSystem_Cache::flushCache();
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method export
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string src
     * @param  string target
     * @param  int revision
     * @return boolean
     */
    public function export($src, $target, $revision = null)
    {
        $returnValue = (bool) false;

        
        $returnValue = core_kernel_versioning_RepositoryProxy::singleton()->export($this, $src, $target, $revision);
        

        return (bool) $returnValue;
    }

    /**
     * @exception core_kernel_versioning_ResourceAlreadyExistsException
     * @exception common_exception_FileAlreadyExists
     * @param options.saveResource {boolean} Save the resource in the onthology
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string src
     * @param  string target
     * @param  string message
     * @param  array options
     * @return core_kernel_versioning_File
     */
    public function import($src, $target, $message = "", $options = array())
    {
        $returnValue = null;

        
        //the src has to be a folder for the moment
        if(!is_dir($src)){
            throw new core_kernel_versioning_exception_Exception('The first parameter has to be a valid folder');
        }
        
        $repositoryUrl = $this->getUrl();
        if(strstr($target, $repositoryUrl) === false){
            throw new core_kernel_versioning_exception_Exception('The parameter target ('.$target.') does not match the repository url ('.$repositoryUrl.')');
        }
        
        $returnValue = core_kernel_versioning_RepositoryProxy::singleton()->import($this, $src, $target, $message, $options);
        
        

        return $returnValue;
    }

    /**
     * Short description of method listContent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string path
     * @param  int revision
     * @return array
     */
    public function listContent($path, $revision = null)
    {
        $returnValue = array();

        
        $returnValue = core_kernel_versioning_RepositoryProxy::singleton()->listContent($this, $path, $revision);
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getUrl
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function getUrl()
    {
        $returnValue = (string) '';

        
        
        $returnValue = $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL));
        
        

        return (string) $returnValue;
    }

    /**
     * Short description of method enable
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return boolean
     */
    public function enable()
    {
        $returnValue = (bool) false;

        
        if ($this->authenticate()) {
        	if($this->checkout()){
        		// has root file?
        		$rootFile = $this->getRootFile();
        		if (!is_null($rootFile)) {
        			// delete the ressource, not the files
        			$ressource = new core_kernel_classes_Resource($rootFile);
        			$ressource->delete();
        		}
				$rootFile = $this->createFile('');
				$this->editPropertyValues(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_ROOTFILE), $rootFile);
        		
        		$this->editPropertyValues(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED), GENERIS_TRUE);
				common_Logger::i("The remote versioning repository ".$this->getUri()." is bound to TAO.");
				core_kernel_fileSystem_Cache::flushCache();
        		$returnValue = true;
        	}
        }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method disable
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return boolean
     */
    public function disable()
    {
        $returnValue = (bool) false;

        
        $classVersionedFiles = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
        $files = $classVersionedFiles->searchInstances(array(
        	PROPERTY_FILE_FILESYSTEM => $this
        ), array('like' => false));
        $rootFile = $this->getRootFile();
        $used = false;
        foreach ($files as $file) {
        	if (is_null($rootFile) || $file->getUri() != $rootFile->getUri()) {
        		$used = true;
        		break;
        	}
        }
        if (!$used) {
			$this->editPropertyValues(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED), GENERIS_FALSE);
			common_Logger::i("The remote versioning repository ".$this->getUri()." has been disabled");
			$returnValue = true;
			core_kernel_fileSystem_Cache::flushCache();
        } else {
			common_Logger::w("The remote versioning repository ".$this->getUri()." could not be disabled, because it is in use by ".$file->getUri());
        }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRootFile
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return core_kernel_versioning_File
     */
    public function getRootFile()
    {
        $returnValue = null;

        
        $rootFiles = $this->getPropertyValues(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_ROOTFILE));
        if (count($rootFiles) == 1) {
        	$returnValue = new core_kernel_versioning_File(current($rootFiles));
        } else {
        	if (count($rootFiles) > 1) {
        		throw new common_Exception("Repository ".$this->getLabel()." has multiple root file");
        	}
		}
        

        return $returnValue;
    }

    /**
     * Ask the repository to deal with a file located in $filePath. It will return
     * you a reference on Versioned File.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string filePath The path to the file you want the repository to deal with.
     * @param  string label A label for the created file Resource.
     * @return core_kernel_versioning_File
     * @since 2.4
     */
    public function spawnFile($filePath, $label = '')
    {
        $returnValue = null;

        
        $fileInfo = new SplFileInfo($filePath);
        $fileName = self::createFileName($fileInfo->getFilename());
        
        $destination = $this->getPath() . $fileName;
        $source = $filePath;
        if(helpers_File::copy($source, $destination, true, false)){
        	
            if ($fileInfo->isDir()) {
                $returnValue = $this->createFile('', $fileName);
            } else {
                $returnValue = $this->createFile($fileName);
            }
        	
        	if (!empty($label)){
        		$returnValue->setLabel($label);
        	}
        }
        

        return $returnValue;
    }

    /**
     * Creates a new file with a specific name and path
     * 
     * @param string $filename
     * @param string $relativeFilePath
     * @return core_kernel_versioning_File
     */
    public function createFile($filename, $relativeFilePath = '') {
    	
        $resource = core_kernel_classes_ResourceFactory::create(new core_kernel_classes_Class(CLASS_GENERIS_FILE));
	    $returnValue = new core_kernel_versioning_File($resource);
	    
	    $returnValue->setPropertiesValues(array(
	    	PROPERTY_FILE_FILENAME => $filename,
	    	PROPERTY_FILE_FILEPATH => trim($relativeFilePath, DIRECTORY_SEPARATOR),
	    	PROPERTY_FILE_FILESYSTEM => $this
	    ));
	    
	    return $returnValue;
    }
    
    /**
     * Create a unique file name on basis of the original one.
     *
     * @access private
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string originalName
     * @return string
     */
    private static function createFileName($originalName)
    {
        $returnValue = (string) '';

        
        $returnValue = uniqid(hash('crc32', $originalName));
        
        $ext = @pathinfo($originalName, PATHINFO_EXTENSION);
        if (!empty($ext)){
        	$returnValue .= '.' . $ext;
        }
        

        return (string) $returnValue;
    }

}