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
?>
<?php

error_reporting(E_ALL);

/**
 * Subversion implementation of a file
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package generis
 
 */
class core_kernel_versioning_subversion_File
        implements core_kernel_versioning_FileInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * singleton
     *
     * @access private
     * @var File
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * Commit File with given message to the given path
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string message
     * @param  string path
     * @param  boolean recursive
     * @return boolean
     * @see core_kernel_versioning_File::commit()
     */
    public function commit( core_kernel_file_File $resource, $message, $path, $recursive = false)
    {
        $returnValue = (bool) false;

        $startTime = helpers_Time::getMicroTime();
        if($resource->getRepository()->authenticate()){
            $paths = is_array($path) ? $path : array($path);
        	$returnValue = svn_commit($message, $paths/*, !$recursive*/);
            $returnValue = $returnValue===false ? false : true;
        }
        $endTime = helpers_Time::getMicroTime();
        common_Logger::i("svn_commit (".$path.') recursive='.($recursive==true?'true':'false').'-> '.($endTime-$startTime).'s');

        return (bool) $returnValue;
    }

    /**
     * Update a file
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  int revision
     * @return boolean
     * @see core_kernel_versioning_File::update()
     */
    public function update( core_kernel_file_File $resource, $path, $revision = null)
    {
        $returnValue = (bool) false;

        
        
        common_Logger::i('svn_update '.$path. ' revision='.$revision);
        if($resource->getRepository()->authenticate()){
            $returnValue = svn_update($path, $revision)===false ? false : true;
        }
        
        

        return (bool) $returnValue;
    }

    /**
     * Revert a file
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  int revision
     * @param  string msg
     * @return boolean
     * @see core_kernel_versioning_File::revert()
     */
    public function revert( core_kernel_file_File $resource, $revision = null, $msg = "")
    {
        $returnValue = (bool) false;

        if($resource->getRepository()->authenticate()){
			
            //no revision, revert local change
            if (is_null($revision)){
                $returnValue = svn_revert($resource->getAbsolutePath());
            }
            else{
				
                $path = realpath($resource->getAbsolutePath());
                common_Logger::i('svn_revert '.$path);

                //get the svn revision number
                $log = svn_log($path);
				$oldRevision = count($log) - $revision;
				
				if(isset($log[$oldRevision])){
					
					$svnRevision = $log[$oldRevision];
					$svnRevisionNumber = $svnRevision['rev'];

					//destroy the existing version
					helpers_VersionedFile::rmWorkingCopy($path);
					
					//replace with the target revision
					if ($resource->update($svnRevisionNumber)) {
						
						if(is_file($path)){
							//get old content
							$content = $resource->getFileContent();
							//update to the current version
							$resource->update();
							//set the new content
							$resource->setContent($content);
							//commit the change
						}
						
						if(is_dir($path)){
							$i = 10;
							do{
								$tmpDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('versionedFolder');
								common_Logger::i(__LINE__.' '.$tmpDir);
								$exists = file_exists($tmpDir);
								$i--;
							}while($exists || !$i);
							
							helpers_VersionedFile::cpWorkingCopy($path, $tmpDir);
							$resource->update();
							//@TODO: empty the current folder $path, to delete no longer versioned files
							helpers_VersionedFile::cpWorkingCopy($tmpDir, $path);
						}
						
						if ($resource->commit($msg)) {
							$returnValue = true;
						}
						//restablish the head version
						else {
							@helpers_VersionedFile::rmWorkingCopy($path);
							$resource->update();
						}
					}
					//restablish the head version
					else {
						@helpers_VersionedFile::rmWorkingCopy($path);
						$resource->update();
					}
				}
                
            }
        }

        return (bool) $returnValue;
    }

    /**
     * Delete a file
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     * @see core_kernel_versioning_File::delete()
     */
    public function delete( core_kernel_file_File $resource, $path)
    {
        $returnValue = (bool) false;

        
        
        $startTime = helpers_Time::getMicroTime();
        if($resource->getRepository()->authenticate()){
            $returnValue = svn_delete($path, true); //force the delete
        }
        $endTime =  helpers_Time::getMicroTime();
        common_Logger::i("svn_delete (".$path.') ->'.($endTime - $startTime).'s');
        
        

        return (bool) $returnValue;
    }

    /**
     * Add a file
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  boolean recursive
     * @param  boolean force
     * @return boolean
     * @see core_kernel_versioning_File::add()
     */
    public function add( core_kernel_file_File $resource, $path, $recursive = false, $force = false)
    {
        $returnValue = (bool) false;

        
        
        $startTime = helpers_Time::getMicroTime();
	    if($resource->getRepository()->authenticate()){
        	$returnValue = svn_add($path, $recursive, $force);
	    }else{
            //throw an Exception
        }
        $endTime = helpers_Time::getMicroTime();
        common_Logger::i("svn_add (".$path.') recursive='.($recursive?'true':'false').' -> '.($endTime-$startTime).'s');
        
        

        return (bool) $returnValue;
    }

    /**
     * Retrieve file 's history
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return array
     * @see core_kernel_versioning_File::gethistory()
     */
    public function getHistory( core_kernel_file_File $resource, $path)
    {
        $returnValue = array();

        
        
        $startTime = helpers_Time::getMicroTime();
        if($resource->getRepository()->authenticate()){
            $returnValue = svn_log($path);
        }
        $endTime = helpers_Time::getMicroTime();
        common_Logger::i('svn_getHistory ('.$path.') -> '.($endTime-$startTime).'s');
        
        

        return (array) $returnValue;
    }

    /**
     * Retrieve file's status
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  array options
     * @return int
     */
    public function getStatus( core_kernel_file_File $resource, $path, $options = array())
    {
        $returnValue = (int) 0;

        $startTime = helpers_Time::getMicroTime();
        
        if($resource->getRepository()->authenticate()){
            
            //Status of the target
            $status = null;
            //Get a list of statuses
            $svnStatusOptions = SVN_NON_RECURSIVE;
            if($options['SHOW_UPDATES']){
                $svnStatusOptions = $svnStatusOptions|SVN_SHOW_UPDATES;
            }
            
            $statuses = @svn_status($path, $svnStatusOptions);
            
            // * An explanation could be that the file is in a non working copy directory, it occured when we create a folders structure
            if($statuses !== false){
                //Extract required status
                foreach($statuses as $s){
                    if($s['path'] == $path){
                        $status = $s;
                    }
                }
                
                // If the file has a status, check the status is not unversioned or added
                if(!is_null($status)){
                    if($status['locked']){
                        $returnValue = VERSIONING_FILE_STATUS_LOCKED;
                    }
                    /**
                     * @todo implement this in the shell implementation
                     */
                    else if($status['repos_text_status'] == VERSIONING_FILE_STATUS_DELETED){
                        $returnValue = VERSIONING_FILE_STATUS_REMOTELY_DELETED;
                    }
                    /**
                     * @todo implement this in the shell implementation
                     */
                    else if($status['repos_text_status'] == VERSIONING_FILE_STATUS_MODIFIED){
                        $returnValue = VERSIONING_FILE_STATUS_REMOTELY_MODIFIED;
                    }
                    else{
                        $returnValue = $status['text_status'];
                    }
                }
                //No status can provide the following information, the file has been versioned & no changes have been made
                else {
                    if(!file_exists($path)){
                        $returnValue = VERSIONING_FILE_STATUS_UNVERSIONED;
                    }
                    else {
                        $returnValue = VERSIONING_FILE_STATUS_NORMAL;
                    }
                }
            }
            //the return of the request is false
            else{
                $returnValue = VERSIONING_FILE_STATUS_UNVERSIONED;
            }
        }
        
        $endTime =  helpers_Time::getMicroTime();
        common_Logger::i("svn_getStatus ('.$path.') '.$returnValue.' -> ".($endTime - $startTime).'s');
        return (int) $returnValue;
    }

    /**
     * Retrieve given version of a file from a path
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  string version
     * @return boolean
     */
    public function resolve( core_kernel_file_File $resource, $path, $version)
    {
        $returnValue = (bool) false;

        $startTime = helpers_Time::getMicroTime();
        $listParentFolder = helpers_File::scandir(dirname($path));
		return core_kernel_versioning_subversionWindows_File::singleton()->resolve($resource, $path, $version);
		
        var_dump('resolving');
        switch($version){
            case VERSIONING_FILE_VERSION_MINE:
                //use our version of the file before the update we made the conflict
                $resource->setContent(file_get_contents($path.'.mine'));
        
                //delete the noisy files (mine, r***)
				var_dump($listParentFolder,$path,  preg_quote($path), '@^' . preg_quote($path) . '\.@');
                foreach($listParentFolder as $file) {
                    if(preg_match('@^' . preg_quote($path) . '\.@', $file)) {
						var_dump('deleted noisy file '.$path);
                        unlink($file);
                    }
                }
                
                $returnValue = true;
                break;
                
            case VERSIONING_FILE_VERSION_THEIRS:
                //use the incoming version of the file
                if($resource->revert()
                   && $resource->update()){
                    $returnValue = true;
                }
                break;
                
            case VERSIONING_FILE_VERSION_WORKING:
                //nothing to do, we keep the current version of the file
                $returnValue = true;
                break;
            
            default:
                //@todo change with invalid argument exception
                throw new common_Exception('invalid argument version');
        }
        
        //$returnValue = core_kernel_versioning_subversionWindows_File::singleton()->resolve($resource, $path, $version);
        $endTime =  helpers_Time::getMicroTime();
        common_Logger::i("svn_resolve ('.$path.' : '.$version.') -> ".($endTime - $startTime).'s');

        return (bool) $returnValue;
    }

    /**
     * Singleton
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_versioning_File
     */
    public static function singleton()
    {
        $returnValue = null;

        if(is_null(self::$instance)){
			self::$instance = new self();
		}
		$returnValue = self::$instance;

        return $returnValue;
    }

}