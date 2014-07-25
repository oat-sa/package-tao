<?php


class GenerisConnector 
extends common_Object
{
	public $importError = '';
	protected $logger;
	protected $generisApi;

	public function __construct($debug = '') {
		$this->debug = $debug;
		$this->generisApi = core_kernel_impl_ApiModelOO::singleton();
	}

	public function importCapi($capiDescriptor) {
		return $capiDescriptor->import();
	}
}
?>
