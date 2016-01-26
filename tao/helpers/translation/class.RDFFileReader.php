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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * An implementation of TranslationFileReader aiming at reading RDF Translation
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_helpers_translation_RDFFileReader
    extends tao_helpers_translation_TranslationFileReader
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method read
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function read()
    {
        
        $translationUnits = array();
        
        try{
            $translationFile = $this->getTranslationFile();
        }
        catch (tao_helpers_translation_TranslationException $e){
            $translationFile = new tao_helpers_translation_RDFTranslationFile();
        }
        
        $this->setTranslationFile($translationFile);
        $inputFile = $this->getFilePath();
        
        if (file_exists($inputFile)){
            if (is_file($inputFile)){
                if (is_readable($inputFile)){
                    
                    try{
                        $doc = new DOMDocument('1.0', 'UTF-8');
                        $doc->load($inputFile);
                        $xpath = new DOMXPath($doc);
                        $rdfNS = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
                        $xmlNS = 'http://www.w3.org/XML/1998/namespace';
                        $xpath->registerNamespace('rdf', $rdfNS);
                        
                        $rootNodes = $xpath->query('//rdf:RDF');
                        if ($rootNodes->length == 1){
                            // Try to get annotations from the root node.
                            $sibling = $rootNodes->item(0)->previousSibling;
                            while ($sibling !== null){
                                if ($sibling instanceof DOMNode && $sibling->nodeType == XML_COMMENT_NODE){
                                    $annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations($sibling->data);
                                    $translationFile->setAnnotations($annotations);
                                    
                                    if (isset($annotations['sourceLanguage'])){
                                        $translationFile->setSourceLanguage($annotations['sourceLanguage']);
                                    }
                                    
                                    if (isset($annotations['targetLanguage'])){
                                        $translationFile->setTargetLanguage($annotations['targetLanguage']);
                                    }
                                    
                                    break;
                                }
                                
                                $sibling = $sibling->previousSibling;
                            }
                            
                            $descriptions = $xpath->query('//rdf:Description');
                            foreach ($descriptions as $description){
                                if ($description->hasAttributeNS($rdfNS, 'about')){
                                    $subject = $description->getAttributeNS($rdfNS, 'about');
                                    
                                    // Let's retrieve properties.
                                    foreach ($description->childNodes as $property){
                                        if ($property->nodeType == XML_ELEMENT_NODE){
                                            // Retrieve namespace uri of this node.
                                            if ($property->namespaceURI != null){
                                                $predicate = $property->namespaceURI . $property->localName;
                                                
                                                // Retrieve an hypothetic target language.
                                                $lang = tao_helpers_translation_Utils::getDefaultLanguage();
                                                if ($property->hasAttributeNS($xmlNS, 'lang')){
                                                    $lang = $property->getAttributeNS($xmlNS, 'lang');
                                                }
                                                
                                                $object = $property->nodeValue;
                                                
                                                $tu = new tao_helpers_translation_RDFTranslationUnit('');
                                                $tu->setTargetLanguage($lang);
                                                $tu->setTarget($object);
                                                $tu->setSubject($subject);
                                                $tu->setPredicate($predicate);
                                                
                                                // Try to get annotations.
                                                $sibling = $property->previousSibling;
                                                while ($sibling !== null){
                                                    if ($sibling instanceof DOMNode && $sibling->nodeType == XML_COMMENT_NODE){
                                                        // We should have the annotations we are looking for.
                                                        $annotations = tao_helpers_translation_RDFUtils::unserializeAnnotations($sibling->data);
                                                        $tu->setAnnotations($annotations);
                                                        
                                                        // Set the found sources and sourcelanguages if found.
                                                        if (isset($annotations['source'])){
                                                            $tu->setSource($annotations['source']);
                                                        }
                                                    }
                                                    
                                                    $sibling = $sibling->previousSibling;
                                                }
                                                
                                                $translationUnits[] = $tu;
                                            }
                                        }
                                    }
                                }
                            }
    
                            $this->getTranslationFile()->addTranslationUnits($translationUnits);
                        }else{
                            throw new tao_helpers_translation_TranslationException("'${inputFile}' has no rdf:RDF root node or more than one rdf:RDF root node.");
                        }
                    }
                    catch (DOMException $e){
                        throw new tao_helpers_translation_TranslationException("'${inputFile}' cannot be parsed.");
                    }
                    
                }else{
                    throw new tao_helpers_translation_TranslationException("'${inputFile}' cannot be read. Check your system permissions.");
                }
            }else{
                throw new tao_helpers_translation_TranslationException("'${inputFile}' is not a file.");
            }
        }else{
            throw new tao_helpers_translation_TranslationException("The file '${inputFile}' does not exist.");
        }
        
    }

} /* end of clas*/

?>