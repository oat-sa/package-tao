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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

/* user defined constants */
// section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001668-constants begin

const VERSIONING_FILE_STATUS_UNVERSIONED        = 2;
const VERSIONING_FILE_STATUS_NORMAL             = 3;
const VERSIONING_FILE_STATUS_ADDED              = 4;
const VERSIONING_FILE_STATUS_MISSING            = 5;
const VERSIONING_FILE_STATUS_DELETED            = 6;
const VERSIONING_FILE_STATUS_REPLACED           = 7;
const VERSIONING_FILE_STATUS_MODIFIED           = 8;
const VERSIONING_FILE_STATUS_CONFLICTED         = 10;
const VERSIONING_FILE_STATUS_REMOTELY_MODIFIED  = 15;
const VERSIONING_FILE_STATUS_REMOTELY_LOCKED    = 16;
const VERSIONING_FILE_STATUS_REMOTELY_DELETED   = 17;

const VERSIONING_FILE_VERSION_MINE              = 'mine-full';
const VERSIONING_FILE_VERSION_THEIRS            = 'theirs-full';
const VERSIONING_FILE_VERSION_WORKING           = 'working';
const VERSIONING_FILE_VERSION_BASE              = 'base';

// section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001668-constants end

/**
 * Manage your versioned files as resources in TAO
 *
 * @access public
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package core
 * @subpackage kernel_versioning
 */
class core_kernel_versioning_File
    extends core_kernel_file_File
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getFileClass
     *
     * @access protected
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return core_kernel_classes_Class
     */
    protected static function getFileClass()
    {
        $returnValue = null;

        // section 10-30-1--78--1698032:13afe62e559:-8000:00000000000030B6 begin
        $returnValue = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
        // section 10-30-1--78--1698032:13afe62e559:-8000:00000000000030B6 end

        return $returnValue;
    }

    /**
     * Short description of method createVersioned
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  string filename
     * @param  relativeFilePath
     * @param  Repository repository
     * @param  string uri
     * @param  array options
     * @return core_kernel_versioning_File
     * @deprecated
     */
    public static function createVersioned($filename, $relativeFilePath = null,  core_kernel_versioning_Repository $repository = null, $uri = '', $options = array())
    {
        $returnValue = null;
        
        $returnValue = $repository->createFile($filename, $relativeFilePath);
		/*
        $repositoryPath = $repository->getPath();
        //add a slash at the end of the repository path if it does not exist
        $repositoryPath = substr($repositoryPath,strlen($repositoryPath)-1,1)==DIRECTORY_SEPARATOR ? $repositoryPath : $repositoryPath.DIRECTORY_SEPARATOR;
        //remove the first slash of the relative path if it exists
        $relativeFilePath = count($relativeFilePath) && $relativeFilePath[0]==DIRECTORY_SEPARATOR ? substr($relativeFilePath,1) : $relativeFilePath;
        //if the relative file path exists format the string
        $relativeFilePath = file_exists($relativeFilePath) ? realpath($relativeFilePath) : $relativeFilePath;
        //add a slash at the end of the relative file path unless the relative file path is empty
        //$relativeFilePath = empty($relativeFilePath) || substr($relativeFilePath,strlen($relativeFilePath)-1,1)==DIRECTORY_SEPARATOR ? $relativeFilePath : $relativeFilePath.DIRECTORY_SEPARATOR;
        
        //build the file path
        $filePath = $repositoryPath.$relativeFilePath;
        
        //Quick hack
        //@todo document and make the change clear
        $filePath = file_exists($filePath) ? realpath($filePath) : $filePath;
        //add directory separator at the end of the file path
        $filePath = substr($filePath,strlen($filePath)-1,1)==DIRECTORY_SEPARATOR ? $filePath : $filePath.DIRECTORY_SEPARATOR;
        
        //check if a resource with the same path exists yet in the repository
        $clazz = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
        $options = array('like' => false, 'recursive' => true);
		$propertyFilter = array(
			PROPERTY_FILE_FILENAME => $filename,
			PROPERTY_FILE_FILEPATH => $filePath,
			PROPERTY_FILE_FILESYSTEM => $repository
		);
        $sameNameFiles = $clazz->searchInstances($propertyFilter, $options);
        if(!empty($sameNameFiles)){
        	throw new core_kernel_versioning_exception_Exception(__('A file with the name "'.$filename.'" already exists at the location '.$repositoryPath.$filePath));
        }
        
        $instance = parent::create($filename, $filePath, $uri);
        $returnValue = new core_kernel_versioning_File($instance);
        
        // Add versioned file path, path of the file in the repository
	    $versionedFilePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $instance->setPropertyValue($versionedFilePathProp, $relativeFilePath);
	    
	    // Add repository
	    $versionedFileRepositoryProp = new core_kernel_classes_Property(PROPERTY_FILE_FILESYSTEM);
	    $instance->setPropertyValue($versionedFileRepositoryProp, $repository);
	    */

        return $returnValue;
    }

    /**
     * Check if a resource is a versioned file resource
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public static function isVersionedFile( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000024B1 begin
        
        $returnValue = $resource->hasType(new core_kernel_classes_Class(CLASS_GENERIS_FILE));
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000024B1 end

        return (bool) $returnValue;
    }

    /**
     * Commit changes to the remote repository
     *
     * Throw a core_kernel_versioning_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * Throw a core_kernel_versioning_FileRemainsInConflictException 
     * if  the local working copy of the resource remains in conflict
     *
     * Throw a core_kernel_versioning_OutOfDateException 
     * if the local working copy of the resource is out of date (and 
     * requires an update)
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  string message
     * @param  boolean recursive
     * @return boolean
     */
    public function commit($message = "", $recursive = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F5 begin
        $status = $this->getStatus();
        
        //check that the file does not remain in conflict
        if($status == VERSIONING_FILE_STATUS_UNVERSIONED){
            throw new core_kernel_versioning_exception_FileUnversionedException();
        }
        
        //check that the file does not remain in conflict
        if($status == VERSIONING_FILE_STATUS_CONFLICTED){
            throw new core_kernel_versioning_exception_FileRemainsInConflictException();
        }
        
        //check that the file does not remain in conflict
        if($status == VERSIONING_FILE_STATUS_REMOTELY_MODIFIED){
            throw new core_kernel_versioning_exception_OutOfDateException();
        }
        
        $returnValue = core_kernel_versioning_FileProxy::singleton()->commit($this, $message, $this->getAbsolutePath(), $recursive);
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F5 end

        return (bool) $returnValue;
    }

    /**
     * Update changes from the remote repository
     * Throw a core_kernel_versioning_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  int revision
     * @return boolean
     */
    public function update($revision = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F7 begin
        $status = $this->getStatus();
        
        //if a revision has been given
        //or the remote version has been modified 
        //or the local working copy does not exist 
        //or the target is a directory
        if( !is_null($revision)
            || is_dir($this->getAbsolutePath()) || (
            $status == VERSIONING_FILE_STATUS_REMOTELY_MODIFIED 
            && $this->fileExists()
        )){
            $returnValue = core_kernel_versioning_FileProxy::singleton()->update($this, $this->getAbsolutePath(), $revision);
        }
        //the file does not require an update, return true
        else{
            $returnValue = true;
        }
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F7 end

        return (bool) $returnValue;
    }

    /**
     * Revert changes
     * Throw a core_kernel_versioning_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  int revision If a revision is given revert changes from this revision. Else revert local changes.
     * @param  string msg
     * @return boolean
     */
    public function revert($revision = null, $msg = "")
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F9 begin
        if($this->fileExists()){
        	if($this->isVersioned()){
        		$returnValue = core_kernel_versioning_FileProxy::singleton()->revert($this, $revision, $msg);
        	}
        }
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F9 end

        return (bool) $returnValue;
    }

    /**
     * Delete the file from the file system.
     * If the versioned file is in conflict solve the problem
     * and delete it.
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete($deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032FC begin
        
        if($this->fileExists()){
        	$filePath = $this->getAbsolutePath();
            //check if the file is up to date
            
            /**
             * @todo this code won't work in shell implentation
             */
            //If the file has yet been deleted remotly => udpate it
            if($this->getStatus() == VERSIONING_FILE_STATUS_REMOTELY_DELETED){
                $returnValue = $this->update();
                /**
                 * @todo check the file is now well deleted locally
                 */
            }
            //else delete it
            else{
                //check if the resource is versioned before the delete
                $isVersioned = $this->isVersioned();
                //if in conflict solve before the problem by using our version of the file
                if($this->isInConflict()){
                    $this->resolve(VERSIONING_FILE_VERSION_MINE);
                }
                //delete the svn resource
                $returnValue = core_kernel_versioning_FileProxy::singleton()->delete($this, $filePath, true);
                //commit the svn delete
                if($returnValue && $isVersioned){
                    //delete the svn resource
                    $returnValue = $this->commit(__('delete the file').' '.$filePath, is_dir($filePath));
                }
            }
            
        }
        else{
            $returnValue = true;
        }
	    
        //delete the tao resource
        $returnValue &= parent::delete($deleteReference);
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032FC end

        return (bool) $returnValue;
    }

    /**
     * Get the repository which is associated to the resource
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return core_kernel_versioning_subversion_Repository
     */
    public function getRepository()
    {
        $returnValue = null;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016DB begin
        
        $repository = $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILESYSTEM));
        if(!is_null($repository)){
        	$returnValue = new core_kernel_versioning_Repository($repository);
        }
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016DB end

        return $returnValue;
    }

    /**
     * Add the resource to the remote repository
     * Throw a core_kernel_versioning_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  boolean recursive
     * @param  boolean force
     * @return boolean
     */
    public function add($recursive = false, $force = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F5 begin
        
        //Check if the path is versioned
        $relativePath = (string) $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH));
        $fileName = (string) $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILENAME));
        $filePath = $this->getRepository()->getPath() . DIRECTORY_SEPARATOR . $relativePath;
        $relativeFilePathExploded = explode(DIRECTORY_SEPARATOR, $relativePath);
        $breadCrumb = realpath($this->getRepository()->getPath());
        
        foreach ($relativeFilePathExploded as $bread) {
            $breadCrumb = realpath($breadCrumb . DIRECTORY_SEPARATOR . $bread);
            if (empty($bread)) {
                continue;
            } 
            //if the resource resource to add is a folder, do not add and commit the resource at this moment
            else if ($breadCrumb == realpath($filePath.DIRECTORY_SEPARATOR.$fileName)) {
                continue;
            }

            if(core_kernel_versioning_FileProxy::singleton()->getStatus($this, $breadCrumb, array('SHOW_UPDATES'=>false)) == VERSIONING_FILE_STATUS_UNVERSIONED){
                core_kernel_versioning_FileProxy::singleton()->add($this, $breadCrumb, null, true);
				core_kernel_versioning_FileProxy::singleton()->commit($this, "[sys] Added the directory ".$bread, $breadCrumb);
            }
            
        }

        //the file does not exist -> EXCEPTION
        if (!$this->fileExists()){
            throw new core_kernel_versioning_exception_Exception(__('Unable to add a file (' . $this->getAbsolutePath() . '). The file does not exist.'));
        }
        
        $returnValue = core_kernel_versioning_FileProxy::singleton()->add($this, $this->getAbsolutePath(), $recursive, $force);
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F5 end

        return (bool) $returnValue;
    }

    /**
     * Check if the resource is versioned
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return boolean
     */
    public function isVersioned()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F8 begin
        $status = $this->getStatus(array('SHOW_UPDATES'=>false));
        if($status
			&& $status	!= VERSIONING_FILE_STATUS_UNVERSIONED
			&& $status	!= VERSIONING_FILE_STATUS_ADDED){
            $returnValue = true;
        }
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F8 end

        return (bool) $returnValue;
    }

    /**
     * Return the history of the resource as an associative array
     * Throw a core_kernel_versioning_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return array
     */
    public function getHistory()
    {
        $returnValue = array();

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016F9 begin
        if(!is_null($this->getRepository())){
        	$returnValue = core_kernel_versioning_FileProxy::singleton()->gethistory($this, $this->getAbsolutePath());
        }
        
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016F9 end

        return (array) $returnValue;
    }

    /**
     * Get the relative path of the resource in the repository
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return string
     */
    public function getPath()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:0000000000001708 begin
        
       	$versionedFilePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $returnValue = $this->getOnePropertyValue($versionedFilePathProp);
        
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:0000000000001708 end

        return (string) $returnValue;
    }

    /**
     * Check if the content of the local version is different
     * from the remote version of the file.
     * Throw a core_kernel_versioning_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return boolean
     */
    public function hasLocalChanges()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-50a804cb:13317e3246f:-8000:0000000000001712 begin
    
        $returnValue = $this->getStatus() == VERSIONING_FILE_STATUS_MODIFIED;
        
        // section 127-0-1-1-50a804cb:13317e3246f:-8000:0000000000001712 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getVersion
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return int
     */
    public function getVersion()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-750fdd52:133644e7bdd:-8000:0000000000001740 begin
        
        $history = $this->getHistory();
        $returnValue = count($history);
        
        // section 127-0-1-1-750fdd52:133644e7bdd:-8000:0000000000001740 end

        return (int) $returnValue;
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  array options
     * @return int
     */
    public function getStatus($options = array())
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001900 begin
		try{
		
			$svnStatusOptions = array();
			$defaultSvnStatusOptions = array('SHOW_UPDATES' => true);
			$svnStatusOptions = array_merge($defaultSvnStatusOptions, $options);

			$returnValue = core_kernel_versioning_FileProxy::singleton()->getStatus($this, $this->getAbsolutePath(), $svnStatusOptions);
		
		}catch(core_kernel_versioning_exception_FileUnversionedException $e){}
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001900 end

        return (int) $returnValue;
    }

    /**
     * Short description of method resolve
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  string version
     * @return boolean
     */
    public function resolve($version)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001926 begin
        
        switch($version){
            case VERSIONING_FILE_VERSION_MINE:
            case VERSIONING_FILE_VERSION_THEIRS:
            case VERSIONING_FILE_VERSION_WORKING:
            case VERSIONING_FILE_VERSION_BASE:
                break;
            default:
                throw new common_Exception('Invalid Argument');
        }
        
        $returnValue = core_kernel_versioning_FileProxy::singleton()->resolve($this, $this->getAbsolutePath(), $version);
        
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001926 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isInConflict
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return boolean
     */
    public function isInConflict()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001929 begin
        
        $returnValue = $this->getStatus()==VERSIONING_FILE_STATUS_CONFLICTED;
        
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001929 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_versioning_File */

?>