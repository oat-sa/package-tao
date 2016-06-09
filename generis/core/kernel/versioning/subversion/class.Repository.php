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
 * Subversion implementation of a Repository
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package generis
 
 */
class core_kernel_versioning_subversion_Repository
		implements core_kernel_versioning_RepositoryInterface
{
	// --- ASSOCIATIONS ---


	// --- ATTRIBUTES ---

	/**
	 *  singleton
	 *
	 * @access private
	 * @var Repository
	 */
	private static $instance = null;

	/**
	 * Authenticated server
	 * @var type array
	 */
	private static $authenticatedRepositories = array();
	
	// --- OPERATIONS ---

	/**
	 * Checkout the repository
	 *
	 * @access public
	 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  Repository vcs
	 * @param  string url
	 * @param  string path
	 * @param  int revision
	 * @return boolean
	 */
	public function checkout( core_kernel_versioning_Repository $vcs, $url, $path, $revision = null)
	{
		$returnValue = (bool) false;

		$startTime = helpers_Time::getMicroTime();
		if($vcs->authenticate()){
			$returnValue = svn_checkout($url, $path, $revision);
		}
		$endTime = helpers_Time::getMicroTime();
		common_Logger::i("svn_checkout (".$url.' -> '.$path.') -> '.($endTime-$startTime).'s');

		return (bool) $returnValue;
	}

	/**
	 * Authenticate to the repository
	 *
	 * @access public
	 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  Repository vcs
	 * @param  string login
	 * @param  string password
	 * @return boolean
	 */
	public function authenticate( core_kernel_versioning_Repository $vcs, $login, $password)
	{
		$returnValue = (bool) false;

		//if the system has already do its authentication to the repository, return the negociation result
		if(isset(self::$authenticatedRepositories[$vcs->getUri()])){
			$returnValue = self::$authenticatedRepositories[$vcs->getUri()];
		}
		//authenticate the system to the repository
		else{
			svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true); // <--- Important for certificate issues!
			svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE, true);
			svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE, true);
			svn_auth_set_parameter(SVN_AUTH_PARAM_DONT_STORE_PASSWORDS, true);

			svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $login);
			svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $password);

			if(@svn_info((string) $vcs->getOnePropertyValue(new core_kernel_classes_property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL)), false) !== false){
				$returnValue = true;
			}
		}
		self::$authenticatedRepositories[$vcs->getUri()] = $returnValue;

		return (bool) $returnValue;
	}

	/**
	 * Export specific source from the repository to the given target at the given revision
	 *
	 * @access public
	 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  Repository vcs
	 * @param  string src
	 * @param  string target
	 * @param  int revision
	 * @return boolean
	 */
	public function export( core_kernel_versioning_Repository $vcs, $src, $target = null, $revision = null)
	{
		$returnValue = (bool) false;

		$startTime = helpers_Time::getMicroTime();
		$revision = is_null($revision) ? -1 : $revision;
		
		if($vcs->authenticate()){
			$returnValue = svn_export($src, $target, true, $revision);
		}
		$endTime = helpers_Time::getMicroTime();
		common_Logger::i("svn_export (".$src.' -> '.$target.') -> '.($endTime-$startTime).'s');

		return (bool) $returnValue;
	}

	/**
	 * Import specific source from the repository to the given target at the given revision
     *
	 * @access public
	 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  Repository vcs
	 * @param  string src
	 * @param  string target
	 * @param  string message
	 * @param  array options
	 * @return core_kernel_file_File
	 */
	public function import( core_kernel_versioning_Repository $vcs, $src, $target, $message = "", $options = array())
	{
		$returnValue = null;

		//Does not work in the current version of php (try later) https://bugs.php.net/bug.php?id=60293
		//$returnValue = svn_import($src, $target, true);
		
		$startTime = helpers_Time::getMicroTime();
		$saveResource = isset($options['saveResource']) && $options['saveResource'] ? true : false;
		
		if(!$vcs->authenticate()){
			throw new core_kernel_versioning_exception_Exception('Authentication failed on fileSource '.$vcs->getUri());
		}
		
		$repositoryUrl = $vcs->getUrl();
		$relativePath = substr($target, strlen($repositoryUrl));
		$absolutePath = $vcs->getPath().$relativePath;

		/*
		// The resource could already exist, this is not a problem
		//check if the resource already exist
		if(helpers_File::resourceExists($absolutePath)){
			throw new core_kernel_versioning_exception_ResourceAlreadyExistsException('The folder ('.$absolutePath.') already exists in the repository ('.$vcs->getPath().')');
		}
		// Same thing here
		//check if the file already exist
		else if(file_exists($absolutePath)){
			throw new common_exception_FileAlreadyExists($absolutePath);
		}
		*/

		//Copy the src folder to the target destination
		helpers_File::copy($src, $absolutePath);

		// Create the importFolder	 
        $importFolder = $vcs->createFile('', $relativePath);
			
//			//Get status of the imported folder
//			$importFolderStatus = $importFolder->getStatus(array('SHOW_UPDATES'=>false));
//			$importFolderYetCommited = true;
//			if($importFolderStatus == VERSIONING_FILE_STATUS_ADDED || $importFolderStatus == VERSIONING_FILE_STATUS_UNVERSIONED){
//				$importFolderYetCommited = false;
//			}
//			
//			//If the import folder has been yet commited, commit its content
//			if($importFolderYetCommited){
//				$filesToCommit = tao_helpers_File::scandir($importFolder->getAbsolutePath());
//				$pathsFilesToCommit = array();
//				foreach($filesToCommit as $fileToCommit){
//					$pathFileToCommit = $fileToCommit->getAbsolutePath();
//					$pathsFilesToCommit[] = $pathFileToCommit;
//					//Add content of the folder if it is not versioned or partially not versioned
//					if(!core_kernel_versioning_FileProxy::add($importFolder, $pathFileToCommit, true, true)){
//						throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The add step encountered a problem');
//					}
//				}
//				//Commit all the files in one commit operation
//				if(!core_kernel_versioning_FileProxy::commit($VersionedUnitFolderInstance, $pathsFilesToCommit)){
//					throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The commit step encountered a problem');
//				}
//			}
//			//Else commit itself
//			else{
//				//Add the folder
//				if(!$importFolder->add(true, true)){
//					throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The add step encountered a problem');
//				}
//				//And commit it
//				if(!$importFolder->commit($message, true)){
//					throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The commit step encountered a problem');
//				}
//			}
			
		//Add the folder
		if(!$importFolder->add(true, true)){
			throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The add step encountered a problem');
		}
		//And commit it
		if(!$importFolder->commit($message, true)){
			throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The commit step encountered a problem');
		}
		
		//Delete the resource if the developer does not want to keep a reference in the onthology
		if($saveResource){
			$returnValue = $importFolder;
		}
		else{
			$resourceToDelete = new core_kernel_classes_Resource($importFolder->getUri());
			$resourceToDelete->delete();
		}
		
		$endTime = helpers_Time::getMicroTime();
		common_Logger::i("svn_import (".$src.' -> '.$target.') -> '.($endTime-$startTime).'s');


		return $returnValue;
	}

	/**
	 * List repository content at a given revision
	 *
	 * @access public
	 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  Repository vcs
	 * @param  string path
	 * @param  int revision
	 * @return array
	 */
	public function listContent( core_kernel_versioning_Repository $vcs, $path, $revision = null)
	{
		$returnValue = array();

		$startTime = helpers_Time::getMicroTime();
		if($vcs->authenticate()){
			$svnList = svn_ls($path, $revision);
			foreach($svnList as $svnEntry){
				$returnValue[] = array(
					 'name'		 => $svnEntry['name']
					 , 'type'	   => $svnEntry['type']
					 , 'revision'   => $svnEntry['created_rev']
					 , 'author'	 => $svnEntry['last_author']
					 , 'time'	   => $svnEntry['time_t']
				);
			}
		}
		$endTime = helpers_Time::getMicroTime();
		common_Logger::i("svn_listContent (".$path.') -> '.($endTime-$startTime).'s');
		
		return (array) $returnValue;
	}

	/**
	 * Singleton
	 *
	 * @access public
	 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @return core_kernel_versioning_subversion_Repository
	 */
	public static function singleton()
	{
		$returnValue = null;


		
		if(is_null(self::$instance)){
			self::$instance = new core_kernel_versioning_subversion_Repository();
		}
		$returnValue = self::$instance;
		


		return $returnValue;
	}

}