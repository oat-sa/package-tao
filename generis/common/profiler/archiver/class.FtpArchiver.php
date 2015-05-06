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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * 
 *
 * @access public
 * @author Sam Sipasseuth, <sam@taotesting.com>
 * @package generis
 
 */
class common_profiler_archiver_FtpArchiver implements common_profiler_archiver_Archiver
{
	
	protected $ftpServer = '127.0.0.1';
	protected $ftpPort = '21';
	protected $ftpUser = 'ftpUser';
	protected $ftpPassword = '123456';
	protected $backup = true;
	protected $file = '';//the file name
	protected $directory = '';//the file name
	protected $maxFileSize = 1048576;
	protected $sentInterval = 3600;
	
	/* (non-PHPdoc)
	 * @see common_profiler_archiver_Archiver::init()
	 */
	public function init($configuration){
		
		$returnValue = false;
		
		if(isset($configuration['ftp_server']) && !empty($configuration['ftp_server'])){
			$this->ftpServer = (string)$configuration['ftp_server'];
			$returnValue = true;
		}
		if(isset($configuration['ftp_port']) && !empty($configuration['ftp_port'])){
			$this->ftpPort = intval($configuration['ftp_port']);
		}
		if(isset($configuration['ftp_user']) && !empty($configuration['ftp_user'])){
			$this->ftpUser = (string)($configuration['ftp_user']);
		}
		if(isset($configuration['ftp_password']) && !empty($configuration['ftp_password'])){
			$this->ftpPassword = intval($configuration['ftp_password']);
		}
		
		if (isset($configuration['directory']) && !empty($configuration['directory'])) {
			if(is_dir($configuration['directory']) && is_writable($configuration['directory'])){
				$this->directory = strval($configuration['directory']);
			}else{
				throw new InvalidArgumentException('the "directory" is not writable');
			}
    	}else{
			throw new InvalidArgumentException('the "directory" is required in the configuration');
		}
		
		if(isset($configuration['sent_time_interval']) && !empty($configuration['sent_time_interval'])){
			$this->sentInterval = intval($configuration['sent_time_interval']);
		}
		
		if(isset($configuration['sent_backup']) && !empty($configuration['sent_backup'])){
			$this->backup = (bool) $configuration['sent_backup'];
		}
		
		$fileName = (isset($configuration['file_name']) && !empty($configuration['file_name'])) ? strval($configuration['file_name']) : 'systemProfiles';
		$this->file = $this->directory.$fileName;
		
		$this->counterFile = $this->directory.'counter';
		
		$this->sentFolder = $this->directory.'sent';
		
    	if (isset($configuration['max_file_size'])) {
    		$this->maxFileSize = $configuration['max_file_size'];
    	}
		
		return $returnValue;
	}
	
	/* (non-PHPdoc)
	 * @see common_profiler_archiver_Archiver::store()
	 */
	public function store($profileData){
		
		$systemDataStr = '';
		if(isset($profileData['context']) && isset($profileData['context']['system'])){
			$systemDataStr = json_encode($profileData['context']['system']);
			unset($profileData['context']['system']);
		}
		
		$profileDataStr = json_encode($profileData);
		if(!file_exists($this->file) && !empty($systemDataStr)){
			//initialize file:
			$profileDataStr = '['.$systemDataStr.','.$profileDataStr;
		}
		
		
		$currentTimestamp = time();
		$send = ($this->maxFileSize > 0 && file_exists($this->file) && filesize($this->file) >= $this->maxFileSize);
		if(!$send){
			if (file_exists($this->counterFile)) {
				$lastSent = intval(file_get_contents($this->counterFile));
				$send = ($currentTimestamp > $lastSent + $this->sentInterval);
			} else {
				//initialize counter file somehow
				file_put_contents($this->counterFile, $currentTimestamp);
			}
		}
		
		$profileDataStr .= ($send)?']':',';//finalize the file by closing the array or continue appending
		file_put_contents($this->file, $profileDataStr, FILE_APPEND);
		
		if($send){
			file_put_contents($this->counterFile, $currentTimestamp);
			$this->send();
			if($this->backup){
				rename($this->file, $this->sentFolder.DIRECTORY_SEPARATOR.'sent_'.$currentTimestamp);
			}
		}
		
	}
	
	/**
	 * @author "Sam Sipasseuth, <sam@taotesting.com>"
	 */
	protected function send(){
		
		// set up a connection or die
		$ftpStream = ftp_connect($this->ftpServer, $this->ftpPort);
		if ($ftpStream == false) {
			common_Logger::d('cannot connect to profiling server', 'PROFILER');
		} else {
			if (ftp_login($ftpStream, $this->ftpUser, $this->ftpPassword)) {
				$system = new common_profiler_System();
				$remoteFile = 'taoProfile_' . $system->getComputerId() . '_' . time();
				$res = ftp_put($ftpStream, $remoteFile, $this->file, FTP_ASCII);//beware: use of sync-blocking TCP protocol
				if (!$res) {
					common_Logger::d('profiles upload failed!', 'PROFILER');
				}
				common_Logger::d('profiles uploaded to analysis server', 'PROFILER');
			} else {
				common_Logger::d('cannot log into ftp server: ' . $this->ftpServer, 'PROFILER');
			}
		}
		
	}
	
	/**
	 * @author "Sam Sipasseuth, <sam@taotesting.com>"
	 */
	protected function clear(){
		
		if(file_exists($this->file)) {
		    helpers_File::remove($this->file);
		}
		if(file_exists($this->counterFile)) {
		    helpers_File::remove($this->counterFile);
		}
		if(file_exists($this->sentFolder)) {
		    helpers_File::remove($this->sentFolder);
		}
		
	}

}