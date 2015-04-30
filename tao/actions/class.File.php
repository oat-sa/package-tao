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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\tao\model\websource\WebsourceManager;
use oat\tao\model\websource\ActionWebSource;
/**
 * 
 * Controller use for the file upload components
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 */
class tao_actions_File extends tao_actions_CommonModule{
	
	/**
	 * @var string $rootFolder root folder of the copyed files
	 */
	protected $rootFolder = '';

	/**
	 * constructor. Initialize the context
	 */
	public function __construct()
	{
		parent::__construct();
		$this->rootFolder = sys_get_temp_dir();
	}
	
	
	/**
	 * Upload a file using http and copy it from the tmp dir to the target folder
	 * @return void
	 */
	public function upload()
	{
		$response = array('uploaded' => false);

		foreach ((array)$_FILES as $file) {
			$targetFolder = isset($_POST['folder']) ? $_POST['folder'] : '/';
			$response = array_merge($response, $this->uploadFile($file, $targetFolder . '/'));
		}
		$response = json_encode($response);
		print $response; 
		return;
	}
	
	public function cancelUpload()
	{
		$removed = 0;
		if($this->hasRequestParameter('filename')){
			$filename = trim($this->getRequestParameter('filename'));
				if(!empty($filename)){
					$pattern = "/^[0-9a-f]*_".preg_quote($filename, "/")."$/";
				
				$targetPath = tao_helpers_File::concat(array($this->rootFolder, $_REQUEST['folder']));
				foreach(scandir($targetPath) as $file){
					if(preg_match($pattern, $file)){
						if(tao_helpers_File::remove($targetPath.'/'.$file)){
							$removed++;
						}
					}
				}
			}
		}
		echo json_encode(array('removed' => $removed));
	}
	
	/**
	 * Produce a simple view use to display a file upload form in a popup
	 */
	public function htmlUpload()
	{	
		if($this->hasRequestParameter('sizeLimit')){
			$this->setData('sizeLimit', (int) $this->getRequestParameter('sizeLimit'));
		}
		else{
			$this->setData('sizeLimit', tao_helpers_Environment::getFileUploadLimit());
		}
		if($this->hasRequestParameter('target')){
			$this->setData('target', $this->getRequestParameter('target'));
		}
		else{
			$this->setData('target', "#source");
		}
		
		
		$this->setData('accept', '*');
		if($this->hasRequestParameter('fileExt')){
			$accept = '';
			foreach(explode(',', $this->getRequestParameter('fileExt')) as $fileExt){
				$accept .= tao_helpers_File::getMimeType(str_replace('*', 'file', $fileExt));
			}
			$this->setData('accept', $accept);
		}
		
		$response = array(
			'uploaded' 		=> false,
			'data'			=> '',
			'name'			=> '',
			'uploaded_file' => ''
		);
		if (isset($_FILES) && isset($_POST['upload_sent'])) {
			if(isset($_FILES['Filedata'])){
				$response = array_merge($response, $this->uploadFile($_FILES['Filedata'], '/'));
			}
		}
		
		$setLinear = true;
		if($this->hasRequestParameter('format')){
			if($this->getRequestParameter('format') != 'linear'){
				$setLinear = false;
			}
		}
		
		$this->setData('setLinear', $setLinear);
		$this->setData('uploaded', ($response['uploaded'] === true));
		$this->setData('uploadData', $response['data']);
		$this->setData('uploadFile', $response['name']);
		$this->setData('uploadFilePath', $response['uploaded_file']);
		$this->setView('form/html_upload.tpl');	
	}
	
	
	/**
	 * Get, check and move the file uploaded (described in the posetedFile parameter)
	 * 
	 * @param array $postedFile
	 * @param string $folder
	 * @return array $data
	 */
	protected function uploadFile($postedFile, $folder)
	{
		$returnValue = array();
		
		if(isset($postedFile['tmp_name']) && isset($postedFile['name'])){
			$tempFile = $postedFile['tmp_name'];
			$targetPath = tao_helpers_File::concat(array($this->rootFolder,$folder));
			if(tao_helpers_File::securityCheck($targetPath)){
				if(!file_exists($targetPath)){
					mkdir($targetPath);
				}
				$targetFile =  tao_helpers_File::concat(array($targetPath, uniqid().'_'.$postedFile['name']));
				if(move_uploaded_file($tempFile, $targetFile)){
					$returnValue['uploaded'] = true;
					$data = $postedFile;
					$data['type'] =  tao_helpers_File::getMimeType($targetFile);
					$data['uploaded_file'] = $targetFile;
					$returnValue['name'] = $postedFile['name'];
					$returnValue['uploaded_file'] = $targetFile;
					$returnValue['data'] = serialize($data);
				}
			}
		}
		return $returnValue;
	}
	
	/**
	 * Download a resource file content
	 * @param {String} uri Uri of the resource file
	 */
	public function downloadFile()
	{
		if($this->hasRequestParameter('uri')){
			$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
			$resource = new core_kernel_classes_Resource($uri);
			if(core_kernel_versioning_File::isVersionedFile($resource) || core_kernel_file_File::isFile($resource)){
				
				$file = new core_kernel_file_File($uri);
				$fileName = $file->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILENAME));
				$content = $file->getFileContent();
				$size = strlen($content);
				$mimeType = tao_helpers_File::getMimeType($file->getAbsolutePath(), true);
				$this->setContentHeader($mimeType);
				
				header("Content-Length: $size");
				header("Content-Disposition: attachment; filename=\"{$fileName}\"");
				header("Expires: 0");
				header("Cache-Control: no-cache, must-revalidate");
				header("Pragma: no-cache");

				print $content;
				return;
			}else{
				throw new Exception('The resource ('.$uri.') is not a valid file resource');
			}
		}
		
	}
	
	public function getFileInfo($uri){
		
		$returnValue = array(
			'name' => __('no file')
		);
		
		$fileResource = null;
		if(!is_null($uri)){
			$fileResource = new core_kernel_file_File($uri);
		}else if(is_null($uri) && $this->hasRequestParameter('uri')){
			$fileResource = new core_kernel_file_File(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
		}
		
		if(is_null($fileResource)){
			throw new Exception('no file uri given');
		}
		
		
		$returnValue['name'] = (string) $fileResource->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILENAME));
		
		if(!is_null($uri)){
			return $returnValue;
		}else{
			echo json_encode($returnValue);
			
		}
	}
	
	public function getPropertyFileInfo(){
		
		$data = array('name' => __('(empty)'));
		if($this->hasRequestParameter('uri') && $this->hasRequestParameter('propertyUri')){
			$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
			$propertyUri = tao_helpers_Uri::decode($this->getRequestParameter('propertyUri'));
			$instance = new core_kernel_classes_Resource($uri);
			$file = $instance->getOnePropertyValue(new core_kernel_classes_Property($propertyUri));
			
			if(!is_null($file) && $file instanceof core_kernel_classes_Resource
				&& core_kernel_file_File::isFile($file)) {
					$data = $this->getFileInfo($file->getUri());
			}
		}
		
		echo json_encode($data);
		
	}
	
	public function accessFile() {
        list($extension, $module, $action, $code, $filePath) = explode('/', tao_helpers_Request::getRelativeUrl(), 5);;
        list($key, $subPath) = explode(' ', base64_decode($code), 2);
        
        $source = WebsourceManager::singleton()->getWebsource($key);
        if ($source instanceof ActionWebSource) {
            $path = $source->getFileSystem()->getPath().$subPath.(empty($filePath) ? '' : DIRECTORY_SEPARATOR.$filePath);
            tao_helpers_Http::returnFile($path);
        }
    }
}
