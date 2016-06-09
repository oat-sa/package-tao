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
 * Copyright (c) 2013-2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data\storage\xml;

use qtism\data\content\RubricBlockRef;
use qtism\data\QtiComponentIterator;
use qtism\data\QtiComponent;
use qtism\data\TestPart;
use qtism\data\ExtendedAssessmentSection;
use qtism\data\AssessmentSectionRef;
use qtism\data\storage\FileResolver;
use qtism\data\ExtendedAssessmentItemRef;
use qtism\data\AssessmentSection;
use qtism\data\AssessmentItemRef;
use qtism\data\storage\LocalFileResolver;
use qtism\data\AssessmentTest;
use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\xml\marshalling\CompactMarshallerFactory;
use \DOMElement;
use \SplObjectStorage;
use \Exception;

class XmlCompactDocument extends XmlDocument {
	
    /**
     * Whether or not the rubricBlock elements
     * must be separated from the core document.
     * 
     * @var boolean
     */
    private $explodeRubricBlocks = false;
    
    /**
     * Whether or not the rubrickBlock components contained
     * in the document should be separated from the document.
     * 
     * If $explodedRubricBlocks is set to true, a call to XmlCompactDocument::save()
     * will:
     * 
     * * rubricBlock components will be removed from the document.
     * * replace the rubricBlock components by rubricBlockRef components with a suitable value for identifier and href attributes.
     * * place the substituted rubricBlock content in separate QTI-XML files, in a valid location and with a valid name regarding the generated rubricBlockRef components.
     * 
     * Please note that this is took under consideration only when the XmlDocument::save() method
     * is used.
     * 
     * @param boolean $explodeRubricBlocks
     */
    public function setExplodeRubricBlocks($explodeRubricBlocks) {
        $this->explodeRubricBlocks = $explodeRubricBlocks;
    }
    
    /**
     * Whether or not the rubricBlock components contained
     * in the document should be separated from the document.
     * 
     * @return boolean
     */
    public function mustExplodeRubricBlocks() {
        return $this->explodeRubricBlocks;
    }
    
	/**
	 * Override of XmlDocument::createMarshallerFactory in order
	 * to return an appropriate CompactMarshallerFactory.
	 * 
	 * @return CompactMarshallerFactory A CompactMarshallerFactory object.
	 */
	protected function createMarshallerFactory() {
		return new CompactMarshallerFactory();
	}
	
	/**
	 * Create a new instance of XmlCompactDocument from an XmlAssessmentTestDocument.
	 *
	 * @param XmlDocument $xmlAssessmentTestDocument An XmlAssessmentTestDocument object you want to store as a compact XML file.
	 * @return XmlCompactDocument An XmlCompactAssessmentTestDocument object.
	 * @throws XmlStorageException If an error occurs while transforming the XmlAssessmentTestDocument object into an XmlCompactAssessmentTestDocument object.
	 */
	public static function createFromXmlAssessmentTestDocument(XmlDocument $xmlAssessmentTestDocument, FileResolver $itemResolver = null) {
	    $compactAssessmentTest = new XmlCompactDocument();
	    $identifier = $xmlAssessmentTestDocument->getDocumentComponent()->getIdentifier();
	    $title = $xmlAssessmentTestDocument->getDocumentComponent()->getTitle();
	    
	    $assessmentTest = new AssessmentTest($identifier, $title);
	    $assessmentTest->setOutcomeDeclarations($xmlAssessmentTestDocument->getDocumentComponent()->getOutcomeDeclarations());
	    $assessmentTest->setOutcomeProcessing($xmlAssessmentTestDocument->getDocumentComponent()->getOutcomeProcessing());
	    $assessmentTest->setTestFeedbacks($xmlAssessmentTestDocument->getDocumentComponent()->getTestFeedbacks());
	    $assessmentTest->setTestParts($xmlAssessmentTestDocument->getDocumentComponent()->getTestParts());
	    $assessmentTest->setTimeLimits($xmlAssessmentTestDocument->getDocumentComponent()->getTimeLimits());
	    $assessmentTest->setToolName($xmlAssessmentTestDocument->getDocumentComponent()->getToolName());
	    $assessmentTest->setToolVersion($xmlAssessmentTestDocument->getDocumentComponent()->getToolVersion());
	
	    // File resolution.
	    $sectionResolver = new LocalFileResolver($xmlAssessmentTestDocument->getUrl());
	
	    if (is_null($itemResolver) === true) {
	        $itemResolver = new LocalFileResolver($xmlAssessmentTestDocument->getUrl());
	    }
	    else {
	        $itemResolver->setBasePath($xmlAssessmentTestDocument->getUrl());
	    }
	
	    // It simply consists of replacing assessmentItemRef and assessmentSectionRef elements.
	    $trail = array(); // trailEntry[0] = a component, trailEntry[1] = from where we are coming (parent).
	    $mark = array();
	    $root = $xmlAssessmentTestDocument->getDocumentComponent();
	
	    array_push($trail, array($root, $root));
	
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
	                        $baseUri = $xmlAssessmentTestDocument->getUrl();
	                        if ($component instanceof XmlDocument && $component->getDocumentComponent() instanceof AssessmentSection) {
	                            $baseUri = $component->getUrl();
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
	            else if ($component instanceof AssessmentSection) {
	                $assessmentSection = ExtendedAssessmentSection::createFromAssessmentSection($component);
	                
	                $previousParts = ($previous instanceof TestPart) ? $previous->getAssessmentSections() : $previous->getSectionParts();
	                foreach ($previousParts as $k => $previousPart) {
	                    if ($previousParts[$k] === $component) {
	                        $previousParts->replace($component, $assessmentSection);
	                        break;
	                    }
	                }
	            }
	            else if ($component === $root) {
	                // 2nd pass on the root, we have to stop.
	                $compactAssessmentTest->setDocumentComponent($assessmentTest);
	                return $compactAssessmentTest;
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
	        	
	        $doc = new XmlDocument();
	        $doc->load($href);
	        	
	        foreach ($doc->getDocumentComponent()->getResponseDeclarations() as $resp) {
	            $compactAssessmentItemRef->addResponseDeclaration($resp);
	        }
	        	
	        foreach ($doc->getDocumentComponent()->getOutcomeDeclarations() as $out) {
	            $compactAssessmentItemRef->addOutcomeDeclaration($out);
	        }
	        	
	        if ($doc->getDocumentComponent()->hasResponseProcessing() === true) {
	            $compactAssessmentItemRef->setResponseProcessing($doc->getDocumentComponent()->getResponseProcessing());
	        }
	        	
	        $compactAssessmentItemRef->setAdaptive($doc->getDocumentComponent()->isAdaptive());
	        $compactAssessmentItemRef->setTimeDependent($doc->getDocumentComponent()->isTimeDependent());
	    }
	    catch (Exception $e) {
	        $msg = "An error occured while unreferencing item reference with identifier '" . $compactAssessmentItemRef->getIdentifier() . "'.";
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
	        	
	        $doc = new XmlDocument();
	        $doc->load($href);
	        return $doc->getDocumentComponent();
	    }
	    catch (XmlStorageException $e) {
            $msg = "An error occured while unreferencing section reference with identifier '" . $assessmentSectionRef->getIdentifier() . "'.";
            throw new XmlStorageException($msg, $e);
	    }
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
	
	    parent::schemaValidate($filename);
	}
	
	/**
	 * Override of XmlDocument.
	 * 
	 * Specifices the correct XSD schema locations and main namespace
	 * for the root element of a Compact XML document.
	 */
	public function decorateRootElement(DOMElement $rootElement) {
		$rootElement->setAttribute('xmlns', "http://www.imsglobal.org/xsd/imsqti_v2p1");
		$rootElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$rootElement->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation', "http://www.taotesting.com/xsd/qticompact_v1p0.xsd");
	}
	
	public function beforeSave(QtiComponent $documentComponent, $uri) {
	    
	    // Take care of rubricBlock explosion. Transform
	    // actual rubricBlocks in rubricBlockRefs.
	    if ($this->mustExplodeRubricBlocks() === true) {
	        
	        // Get all rubricBlock elements...
	        $iterator = new QtiComponentIterator($documentComponent, array('rubricBlock'));
	        $sectionCount = new SplObjectStorage();
	        
	        foreach ($iterator as $rubricBlock) {
	            // $section contains the assessmentSection the rubricBlock is related to.
	            $section = $iterator->parent();
	            
	            // determine the occurence number of the rubricBlock relative to its section.
	            if (isset($sectionCount[$section]) === false) {
	                $sectionCount[$section] = 0;
	            }
	            
	            $sectionCount[$section] = $sectionCount[$section] + 1;
	            $occurence = $sectionCount[$section];
	            
	            // determine a suitable file name for the external rubricBlock definition.
	            $rubricBlockRefId = 'RB_' . $section->getIdentifier() . '_' . $occurence;
	            $href = './rubricBlock_' . $rubricBlockRefId . '.xml';
	            
	            $doc = new XmlDocument();
	            $doc->setDocumentComponent($rubricBlock);
	            
	            try {
	                $pathinfo = pathinfo($uri);
	                $doc->save($pathinfo['dirname'] . DIRECTORY_SEPARATOR . $href);
	                
	                // replace the rubric block with a reference.
	                $sectionRubricBlocks = $section->getRubricBlocks();
	                $sectionRubricBlocks->remove($rubricBlock);
	                
	                $sectionRubricBlockRefs = $section->getRubricBlockRefs();
	                $sectionRubricBlockRefs[] = new RubricBlockRef($rubricBlockRefId, $href);
	            }
	            catch (XmlStorageException $e) {
	                $msg = "An error occured while creating external rubrickBlock definition(s).";
	                throw new XmlStorageException($msg, $e);
	            }
	        }
	    }
	}
}
