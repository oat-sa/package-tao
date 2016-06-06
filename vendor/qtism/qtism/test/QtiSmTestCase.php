<?php
require_once(dirname(__FILE__) . '/../qtism/qtism.php');

use qtism\data\storage\xml\marshalling\MarshallerFactory;

abstract class QtiSmTestCase extends PHPUnit_Framework_TestCase {
	
	private $marshallerFactory;
	
	public function setUp() {
	    parent::setUp();
		$this->marshallerFactory = new MarshallerFactory();
	}
	
	public function tearDown() {
	    parent::tearDown();
	    unset($this->marshallerFactory);
	}
	
	public function getMarshallerFactory() {
		return $this->marshallerFactory;
	}
	
	/**
	 * Returns the canonical path to the samples directory, with the
	 * trailing slash.
	 * 
	 * @return string
	 */
	public static function samplesDir() {
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR;
	}
	
	/**
	 * Create a directory in OS temp directory with a unique name.
	 * 
	 * @return string The path to the created directory.
	 */
	public static function tempDir() {
	    $tmpFile = tempnam(sys_get_temp_dir(), 'qsm');
	    
	    // Tempnam creates a file with 600 chmod. Remove
	    // it and create a directory.
	    if (file_exists($tmpFile) === true) {
	        unlink($tmpFile);
	    }
	    
	    mkdir($tmpFile);
	    
	    return $tmpFile;
	}
	
	/**
	 * Create a copy of $source to the temp directory. The copied
	 * file will receive a unique file name.
	 * 
	 * @param string $source The source file to be copied.
	 * @return string The path to the copied file.
	 */
	public static function tempCopy($source) {
	    $tmpFile = tempnam(sys_get_temp_dir(), 'qsm');
	    
	    // Same as for QtiSmTestCase::tempDir...
	    if (file_exists($tmpFile) === true) {
	        unlink($tmpFile);
	    }
	    
	    copy($source, $tmpFile);
	    
	    return $tmpFile;
	}
	
	/**
	 * Create a DOMElement from an XML string.
	 * 
	 * @param unknown_type $xmlString A string containing XML markup
	 * @return DOMElement The according DOMElement;
	 */
	public static function createDOMElement($xmlString) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML($xmlString);
		return $dom->documentElement;
	}
	
	/**
	 * Create a QtiComponent object from an XML String.
	 *
	 * @param string $xmlString An XML String to transform in a QtiComponent object.
	 * @return \qtism\data\QtiComponent
	 */
	public function createComponentFromXml($xmlString) {
		$element = QtiSmTestCase::createDOMElement($xmlString);
		$factory = $this->getMarshallerFactory();
		$marshaller = $factory->createMarshaller($element);
		return $marshaller->unmarshall($element);
	}
}
