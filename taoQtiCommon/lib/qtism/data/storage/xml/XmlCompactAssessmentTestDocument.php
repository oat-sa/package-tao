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

use qtism\data\storage\FileResolver;
use qtism\data\storage\LocalFileResolver;
use qtism\data\AssessmentSectionRef;
use qtism\data\ExtendedAssessmentItemRef;
use qtism\data\AssessmentItemRef;
use qtism\data\storage\xml\XmlStorageException;
use qtism\data\storage\xml\XmlAssessmentTestDocument;
use qtism\data\AssessmentTest;
use qtism\data\storage\xml\IXmlDocument;
use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\xml\XmlAssessmentItemDocument;
use qtism\data\storage\xml\XmlAssessmentSectionDocument;
use \DOMDocument;
use \Exception;

class XmlCompactAssessmentTestDocument extends AssessmentTest implements IXmlDocument {
	
	/**
	 * The QTI Compact version in use. Default is 1.0.
	 * 
	 * @var string
	 */
	private $version = '1.0';
	
	/**
	 * The XMLDocument object corresponding to the saved/loaded compacted AssessmentTest.
	 * 
	 * @var XMLDocument
	 */
	private $xmlDocument = null;
	
	public function __construct($version = '1.0') {
		parent::__construct('assessmentTest', 'A QTI Assessment Test');
		$this->setXmlDocument(new XmlCompactDocument($version, $this));
		$this->setVersion($version);
	}
	
	public function load($uri, $validate = false) {
		$this->getXmlDocument()->load($uri, $validate);
	}
	
	public function save($uri) {
		$this->getXmlDocument()->save($uri);
	}
	
	public function getUri() {
		return $this->getXmlDocument()->getUri();
	}
	
	/**
	 * Validate the compact AssessmentTest XML document according to the relevant XSD schema.
	 * If $filename is provided, the file pointed by $filename will be used instead
	 * of the default schema.
	 */
	public function schemaValidate($filename = '') {
		if (empty($filename)) {
			$dS = DIRECTORY_SEPARATOR;
			// default xsd for AssessmentTest.
			$filename = dirname(__FILE__) . $dS . 'schemes' . $dS . 'qticompact_v1p0.xsd';
		}
		
		$this->getXmlDocument()->schemaValidate($filename);
	}
	
	public function setVersion($version) {
		$this->getXmlDocument()->setVersion($version);
	}
	
	public function getVersion() {
		return $this->getXmlDocument()->getVersion();
	}
	
	protected function setXmlDocument(XmlDocument $xmlDocument) {
		$this->xmlDocument = $xmlDocument;
	}
	
	public function getXmlDocument() {
		return $this->xmlDocument;
	}
	
	/**
	 * Create a new instance of XmlCompactAssessmentTestDocument from an XmlAssessmentTestDocument.
	 * 
	 * @param XmlAssessmentTestDocument $xmlAssessmentTestDocument An XmlAssessmentTestDocument object you want to store as a compact XML file.
	 * @return XmlCompactAssessmentTestDocument An XmlCompactAssessmentTestDocument object.
	 * @throws XmlStorageException If an error occurs while transforming the XmlAssessmentTestDocument object into an XmlCompactAssessmentTestDocument object.
	 */
	public static function createFromXmlAssessmentTestDocument(XmlAssessmentTestDocument $xmlAssessmentTestDocument, FileResolver $itemResolver = null) {
		$compactXml = new static();
		$compactXml->setIdentifier($xmlAssessmentTestDocument->getIdentifier());
		$compactXml->setOutcomeDeclarations($xmlAssessmentTestDocument->getOutcomeDeclarations());
		$compactXml->setOutcomeProcessing($xmlAssessmentTestDocument->getOutcomeProcessing());
		$compactXml->setTestFeedbacks($xmlAssessmentTestDocument->getTestFeedbacks());
		$compactXml->setTestParts($xmlAssessmentTestDocument->getTestParts());
		$compactXml->setTimeLimits($xmlAssessmentTestDocument->getTimeLimits());
		$compactXml->setTitle($xmlAssessmentTestDocument->getTitle());
		$compactXml->setToolName($xmlAssessmentTestDocument->getToolName());
		$compactXml->setToolVersion($xmlAssessmentTestDocument->getToolVersion());
		
		// File resolution.
		$sectionResolver = new LocalFileResolver($xmlAssessmentTestDocument->getUri());
		
		if (is_null($itemResolver) === true) {
		    $itemResolver = new LocalFileResolver($xmlAssessmentTestDocument->getUri());
		}
		else {
		    $itemResolver->setBasePath($xmlAssessmentTestDocument->getUri());
		}
		
		// It simply consists of replacing assessmentItemRef and assessmentSectionRef elements.
		$trail = array(); // trailEntry[0] = a component, trailEntry[1] = from where we are coming (parent).
		$mark = array();
		$root = $xmlAssessmentTestDocument;
		
		array_push($trail, array($xmlAssessmentTestDocument, $root));
		
		while (count($trail > 0)) {
			$trailer = array_pop($trail);
			$component = $trailer[0];
			$previous = $trailer[1];
			
			if (!in_array($component, $mark) && count($component->getComponents()) > 0) {
				
				// First pass on a hierarchical node... go deeper in the n-ary tree.
				array_push($mark, $component);
				
				// We want to go back on this component.
				array_push($trail, $trailer);
				
				// Prepare further exploration.
				foreach ($component->getComponents()->getArrayCopy() as $comp) {
					array_push($trail, array($comp, $component));
				}
			}
			else if (in_array($component, $mark) || count($component->getComponents()) === 0) {
				
				// Second pass on a hierarchical node (we are bubbling up accross the n-ary tree)
				// OR
				// Leaf node
				if ($component instanceof AssessmentItemRef) {
					// Transform the ref in an compact extended ref.
					$compactRef = ExtendedAssessmentItemRef::createFromAssessmentItemRef($component);
					
					// find the old one and replace it.
					$previousParts = $previous->getSectionParts();
					foreach ($previousParts as $k => $previousPart) {
						if ($previousParts[$k] === $component) {
							
							// If the previous processed component is an XmlAssessmentSectionDocument,
							// it means that the given baseUri must be adapted.
							$baseUri = $xmlAssessmentTestDocument->getUri();
							if ($component instanceof XmlAssessmentSectionDocument) {
								$baseUri = $component->getUri();
							}
							
							$itemResolver->setBasePath($baseUri);
							self::resolveAssessmentItemRef($compactRef, $itemResolver);
							$previousParts->replace($component, $compactRef);
							break;
						}
					}
				}
				else if ($component instanceof AssessmentSectionRef) {
					// We follow the unreferenced AssessmentSection as if it was
					// the 1st pass.
					$assessmentSection = self::resolveAssessmentSectionRef($component, $sectionResolver);
					$previousParts = $previous->getSectionParts();
					foreach ($previousParts as $k => $previousPart) {
						if ($previousParts[$k] === $component) {
							$previousParts->replace($component, $assessmentSection);
							break;
						}
					}
					
					array_push($trail, array($assessmentSection, $previous));
				}
				else if ($component === $root) {
					// 2nd pass on the root, we have to stop.
					return $compactXml;
				}
			}
		}
	}
	
	/**
	 * Dereference the file referenced by an assessmentItemRef and add
	 * outcome/responseDeclarations to the compact one.
	 * 
	 * @param ExtendedAssessmentItemRef $compactAssessmentItemRef A previously instantiated ExtendedAssessmentItemRef object.
	 * @param FileResolver $resolver The Resolver to be used to resolver AssessmentItemRef's href attribute.
	 * @throws XmlStorageException If an error occurs (e.g. file not found at URI or unmarshalling issue) during the dereferencing.
	 */
	protected static function resolveAssessmentItemRef(ExtendedAssessmentItemRef $compactAssessmentItemRef, FileResolver $resolver) {
		try {
			$href = $resolver->resolve($compactAssessmentItemRef->getHref());
			
			$doc = new XmlAssessmentItemDocument();
			$doc->load($href);
			
			foreach ($doc->getResponseDeclarations() as $resp) {
				$compactAssessmentItemRef->addResponseDeclaration($resp);
			}
			
			foreach ($doc->getOutcomeDeclarations() as $out) {
				$compactAssessmentItemRef->addOutcomeDeclaration($out);
			}
			
			if ($doc->hasResponseProcessing() === true) {
				$compactAssessmentItemRef->setResponseProcessing($doc->getResponseProcessing());
			}
			
			$compactAssessmentItemRef->setAdaptive($doc->isAdaptive());
			$compactAssessmentItemRef->setTimeDependent($doc->isTimeDependent());
		}
		catch (Exception $e) {
			$msg = "An error occured while unreferencing file '${href}'.";
			throw new XmlStorageException($msg, $e);
		}
	}
	
	/**
	 * Dereference the file referenced by an assessmentSectionRef.
	 * 
	 * @param AssessmentSectionRef $assessmentSectionRef An AssessmentSectionRef object to dereference.
	 * @param FileResolver $resolver The Resolver object to be used to resolve AssessmentSectionRef's href attribute.
	 * @throws XmlStorageException If an error occurs while dereferencing the referenced file.
	 * @return XmlAssessmentSection The AssessmentSection referenced by $assessmentSectionRef.
	 */
	protected static function resolveAssessmentSectionRef(AssessmentSectionRef $assessmentSectionRef, FileResolver $resolver) {
		try {
			$href = $resolver->resolve($assessmentSectionRef->getHref());
			
			$doc = new XmlAssessmentSectionDocument('2.1');
			$doc->load($href);
			return $doc;
		}
		catch (XmlStorageException $e) {
			$msg = "An error occured while unreferencing file '${href}'.";
			throw new XmlStorageException($msg);
		}
	}
}
