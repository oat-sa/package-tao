<?php

require_once "phing/types/DataType.php";

class GenerisConfig extends DataType {

	private $moduleUrl;
	private $moduleMode;
	private $moduleNs;
	private $instanceName;
	private $timezone = null;
	private $extensions = null;
	private $dataPath;


	public function setModuleUrl($moduleUrl){
		$this->moduleUrl = $moduleUrl;
	}

	public function setModuleMode($moduleMode){
		$this->moduleMode = $moduleMode;
	}

	public function setModuleNs($moduleNs){
		$this->moduleNs = $moduleNs;
	}

	public function setInstanceName($instanceName){
		$this->instanceName = $instanceName;
	}

	public function setTimezone($tz){
		$this->timezone = $tz;
	}
	public function setExtensions($ext){
		$this->extensions = $ext;
	}

	public function setDataPath($file_path){
		$this->dataPath = $file_path;
	}

	public function toArray(){
		return array(

			"instance_name"		=> $this->instanceName,
			"module_url"		=> $this->moduleUrl,
			"module_mode"		=> $this->moduleMode,
			"module_namespace"	=> $this->moduleNs,
			"module_lang"		=> "en-US",
			'timezone'   		=> $this->timezone != null ? $this->timezone : date_default_timezone_get(),
			"file_path" 		=> $this->dataPath,
			"extensions"		=> $this->extensions != null ? $this->extensions : 'taoCe' 

		);
	}
}