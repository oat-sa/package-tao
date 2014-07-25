<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *   
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data\storage\xml;

use qtism\data\storage\xml\marshalling\MarshallerFactory;
use qtism\data\AssessmentTest;
use qtism\data\storage\xml\marshalling\Marshaller;
use qtism\data\storage\xml\marshalling\UnmarshallingException;
use qtism\data\QtiComponent;
use qtism\data\storage\Utils as StorageUtils;
use qtism\data\storage\xml\Utils as XmlUtils;
use \DOMDocument;
use \DOMElement;
use \DOMException;
use \RuntimeException;
use \InvalidArgumentException;

/**
 * This class represents a QTI-XML Document.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class XmlDocument implements IXmlDocument {
	
	/**
	 * The QTI version used in the XmlDocument.
	 * 
	 * @var string
	 */
	private $version = '2.1';
	
	/**
	 * The root QTI Component of the parsed XmlDocument.
	 * 
	 * @var QtiComponent
	 */
	private $documentComponent = null;
	
	/**
	 * The produced domDocument after a successful call to
	 * XmlDocument::load or XmlDocument::save.
	 * 
	 * @var DOMDocument
	 */
	private $domDocument = null;
	
	/**
	 * The URI describing how/where the document is located in a serialized state.
	 * 
	 * @var string
	 */
	private $uri = '';
	
	/**
	 * Create a new XmlDocument.
	 * 
	 *
	 * @param string $version The version of the QTI specfication to use in order to load or save an AssessmentTest.
	 * @param QtiComponent $documentComponent (optional) A QtiComponent object to be bound to the QTI XML document to save.
	 */
	public function __construct($version, QtiComponent $documentComponent = null) {
		$this->setVersion($version);
		$this->setDocumentComponent($documentComponent);
	}
	
	/**
	 * Set the QTI version in use.
	 * 
	 * @param string $version The QTI version e.g. 2.1.
	 */
	public function setVersion($version) {
		$this->version = $version;
	}
	
	/**
	 * Get the QTI version in use.
	 * 
	 * @return string The QTI version in use.
	 */
	public function getVersion() {
		return $this->version;
	}
	
	public function getUri() {
		return $this->uri;
	}
	
	protected function setUri($uri = '') {
		$this->uri = StorageUtils::sanitizeUri($uri);
	}
	
	/**
	 * Set the root QTI component of the parsed XmlDocument.
	 * 
	 * @return QtiComponent A QTI Component.
	 */
	public function setDocumentComponent(QtiComponent $ownerComponent = null) {
		$this->documentComponent = $ownerComponent;
	}
	
	/**
	 * Get the root QTI component of the parsed XmlDocument.
	 * 
	 * @return QtiComponent A QTI Component.
	 */
	public function getDocumentComponent() {
		return $this->documentComponent;
	}
	
	/**
	 * Set the DOMDocument object in use.
	 * 
	 * @param DOMDocument $domDocument A DOMDocument object.
	 */
	protected function setDomDocument(DOMDocument $domDocument) {
		$this->domDocument = $domDocument;
	}
	
	/**
	 * Get the DOMDocument object in use.
	 */
	public function getDomDocument() {
		return $this->domDocument;
	}
	
	public function getXmlDocument() {
		return $this;
	}
	
	/**
	 * Load a QTI-XML assessment file. The file will be loaded and represented in
	 * an AssessmentTest object.
	 * 
	 * @param string $uri The Uniform Resource Identifier that identifies/locate the file.
	 * @param sboolean $validate XML Schema validation? Default is false.
	 * @throws XmlStorageException If an error occurs while loading the QTI-XML file.
	 */
	public function load($uri, $validate = false) {
		if (is_readable($uri) && is_file($uri)) {
			try {
				$this->setDomDocument(new DOMDocument('1.0', 'UTF-8'));
				
				// disable xml warnings and errors and fetch error information as needed.
				$oldErrorConfig = libxml_use_internal_errors(true);
				
				if ($this->getDomDocument()->load($uri, LIBXML_COMPACT|LIBXML_NONET|LIBXML_XINCLUDE)) {
					
					// Infer the QTI version.
					if (($version = XmlUtils::inferQTIVersion($this->getDomDocument())) !== false) {
						$this->setVersion($version);
					}
					else {
						$msg = "Cannot infer QTI version for file located at '${uri}'. Is it well formed?";
						throw new XmlStorageException($msg);
					}
					
					if ($validate === true) {
						$this->schemaValidate();
					}
					
					try {
						// Get the root element and unmarshall.
						$element = $this->getDomDocument()->documentElement;
						$factory = $this->createMarshallerFactory();
						$marshaller = $factory->createMarshaller($element);
						$this->setDocumentComponent($marshaller->unmarshall($element, $this->getDocumentComponent()));
						
						// We now are sure that the URI is valid.
						$this->setUri($uri);
					}
					catch (UnmarshallingException $e) {
						$line = $e->getDOMElement()->getLineNo();
						$msg = "An error occured while processing QTI-XML file located at '${uri}' at line ${line}.";
						throw new XmlStorageException($msg, $e);
					}
				}
				else {
					$libXmlErrors = libxml_get_errors();
					$formattedErrors = self::formatLibXmlErrors($libXmlErrors);
					
					libxml_clear_errors();
					libxml_use_internal_errors($oldErrorConfig);
					
					$msg = "An internal occured while parsing QTI-XML file located at '${uri}':\n${formattedErrors}";
					throw new XmlStorageException($msg, null, new LibXmlErrorCollection($libXmlErrors));
				}
			}
			catch (DOMException $e) {
				$line = $e->getLine();
				$msg = "An error occured while parsing QTI-XML file located at '${uri}' at line ${line}.";
				throw new XmlStorageException($msg, $e);
			}
		}
		else {
			$msg = "QTI-XML file located at '${uri}' cannot be open. Please check if the file exists or if it is readable.";
			throw new XmlStorageException($msg);
		}
	}
	
	/**
	 * Save the Assessment Document at the location described by $uri. Please be carefull
	 * to provide an AssessmentTest object to save before calling this method.
	 * 
	 * @param string $uri The URI describing the location to save the QTI-XML representation of the Assessment Test.
	 * @param boolean $formatOutput Wether the XML content of the file must be formatted (new lines, indentation) or not.
	 * @throws XmlStorageException If an error occurs while transforming the AssessmentTest object to its QTI-XML representation.
	 */
	public function save($uri, $formatOutput = true) {
		$assessmentTest = $this->getDocumentComponent();
		
		if (!empty($assessmentTest)) {
			$this->setDomDocument(new DOMDocument('1.0', 'UTF-8'));
			
			if ($formatOutput == true) {
				$this->getDomDocument()->formatOutput = true;
			}
			
			try {
				$factory = $this->createMarshallerFactory();
				$marshaller = $factory->createMarshaller($this->getDocumentComponent());
				$element = $marshaller->marshall($this->getDocumentComponent());
				
				$rootElement = $this->getDomDocument()->importNode($element, true);
				$this->getDomDocument()->appendChild($rootElement);
				$this->decorateRootElement($rootElement);
				
				if ($this->getDomDocument()->save($uri) === false) {
					// An error occured while saving.
					$msg = "An internal error occured while saving QTI-XML file at '${uri}'.";
					throw new XmlStorageException($msg);
				}
				else {
					$this->setUri($uri);
				}
			}
			catch (DOMException $e) {
				$msg = "An internal error occured while saving QTI-XML file at '${uri}'. Please check if the path exists or if it is writable.";
				throw new XmlStorageException($msg, $e);
			}
		}
		else {
			$msg = "The Assessment Document cannot be saved. No AssessmentTest object provided.";
			throw new XmlStorageException($msg);
		}
	}
	
	public function schemaValidate($filename = '') {
		if (empty($filename)) {
			$filename = XmlUtils::getSchemaLocation($this->getVersion());
		}
		
		if (is_readable($filename)) {
			
			$oldErrorConfig = libxml_use_internal_errors(true); 
			
			$doc = $this->getDomDocument();
			if ($doc->schemaValidate($filename) === false) {
				
				$libXmlErrors = libxml_get_errors();
				$formattedErrors = self::formatLibXmlErrors($libXmlErrors);
				
				libxml_clear_errors();
				libxml_use_internal_errors($oldErrorConfig);

				$msg = "The document could not be validated with schema '${filename}':\n${formattedErrors}";
				throw new XmlStorageException($msg, null, new LibXmlErrorCollection($libXmlErrors));
			}
		}
		else {
			$msg = "Schema '${filename}' cannot be read. Does this file exist? Is it readable?";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Decorate the root element of the XmlAssessmentDocument with the appropriate
	 * namespaces and schema definition.
	 *
	 * @param DOMElement $rootElement The root DOMElement object of the document to decorate.
	 */
	protected function decorateRootElement(DOMElement $rootElement) {
		$qtiSuffix = 'v2p1';
		$xsdLocation = 'http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd';
		switch (trim($this->getVersion())) {
			case '2.0':
				$qtiSuffix = 'v2p0';
				$xsdLocation = 'http://www.imsglobal.org/xsd/imsqti_v2p0.xsd';
			break;
		}
		
		$rootElement->setAttribute('xmlns', "http://www.imsglobal.org/xsd/imsqti_${qtiSuffix}");
		$rootElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$rootElement->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation', "http://www.imsglobal.org/xsd/imsqti_${qtiSuffix} ${xsdLocation}");
	}
	
	protected static function formatLibXmlErrors(array $libXmlErrors) {
		$formattedErrors = array();
			
		foreach ($libXmlErrors as $error) {
			switch ($error->level) {
				case LIBXML_ERR_WARNING:
					$formattedErrors[] = "Warning: " . trim($error->message) . " at " . $error->line . ":" . $error->column . ".";
					break;
						
				case LIBXML_ERR_ERROR:
					$formattedErrors[] = "Error: " . trim($error->message) . " at " . $error->line . ":" . $error->column . ".";
					break;
						
				case LIBXML_ERR_FATAL:
					$formattedErrors[] = "Fatal Error: " . trim($error->message) . " at " . $error->line . ":" . $error->column . ".";
					break;
			}
		}
			
		$formattedErrors = implode("\n", $formattedErrors);
		return $formattedErrors;
	}
	
	/**
	 * MarshallerFactory factory method (see gang of four).
	 * 
	 * @return MarshallerFactory An appropriate MarshallerFactory object.
	 */
	protected function createMarshallerFactory() {
		return new MarshallerFactory();
	}
}
