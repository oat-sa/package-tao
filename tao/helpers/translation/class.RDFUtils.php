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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Aims at providing utility methods for RDF Translation models.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */

/**
 * Aims at providing utility methods for RDF Translation models.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_helpers_translation_RDFUtils
{

    /**
     * Unserialize an RDFTranslationUnit annotation and returns an associative
     * where keys are annotation names, and values are the annotation values.
     * Throws TranslationException.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string annotations The annotations string.
     * @return array
     */
    public static function unserializeAnnotations($annotations)
    {
        $returnValue = array();

        $reg = "/\s*@(subject|predicate|sourceLanguage|targetLanguage|source)[\t ]+(.+)(?:\s*|$)/u";
        $matches = array();
        if (false !== preg_match_all($reg, $annotations, $matches)){
            // No problems with $reg.
            if (isset($matches[1])){
                // We got some annotations.
                for ($i = 0; $i < count($matches[1]); $i++){
                    // Annotation name $i processing. Do we have a value for it?
                    $name = $matches[1][$i];
                    if (isset($matches[2][$i])){
                        // We have an annotation with a name and a value.
                        // Do not forget to unescape '--' that is not accepted in XML comments (see spec).
                        // (str_replace is unicode safe ;)!)
                        $value = $matches[2][$i];
                        $value = str_replace("\\-\\-", '--', $value);
                        $value = str_replace("\\\\", "\\", $value);
                        $returnValue[$name] = $value;
                    }
                }
            }
        }else{
            throw new tao_helpers_translation_TranslationException("A fatal error occured while parsing annotations '${annotations}.'");
        }

        return (array) $returnValue;
    }

    /**
     * Serializes an associative array of annotations where keys are annotation
     * and values are annotation values.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array annotations An associative array that represents a collection of annotations, where keys are the annotation names and values the annotation values.
     * @param  string glue Indicates what is the glue between serialized annotations.
     * @return string
     */
    public static function serializeAnnotations($annotations, $glue = '')
    {
        $returnValue = (string) '';

        // Set default glue.
        if ($glue == ''){
            $glue = "\n    ";
        }
        
        $a = array();
        foreach ($annotations as $n => $v){
            $v = str_replace("\\", "\\\\", $v);
            $v = str_replace('--', "\\-\\-", $v);
            $a[] = '@' . trim($n) . " ${v}";
        }
        $returnValue = implode($glue, $a);

        return (string) $returnValue;
    }

    /**
     * Creates a language description file for TAO using the RDF-XML language.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string code string code The language code e.g. fr-FR.
     * @param  string label string label The language label e.g. French in english.
     * @return DomDocument
     */
    public static function createLanguageDescription($code, $label)
    {
        $returnValue = null;

        $languageType = CLASS_LANGUAGES;
        $languagePrefix = 'http://www.tao.lu/Ontologies/TAO.rdf#Lang';
        $rdfNs = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
        $rdfsNs = 'http://www.w3.org/2000/01/rdf-schema#';
        $xmlNs = 'http://www.w3.org/XML/1998/namespace';
        $xmlnsNs = 'http://www.w3.org/2000/xmlns/';
        $base = 'http://www.tao.lu/Ontologies/TAO.rdf#';
        
        $doc = new DomDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;
        
        $rdfNode = $doc->createElementNS($rdfNs, 'rdf:RDF');
        $rdfNode->setAttributeNS($xmlNs, 'xml:base', $base);
        $doc->appendChild($rdfNode);
        
        $descriptionNode = $doc->createElementNS($rdfNs, 'rdf:Description');
        $descriptionNode->setAttributeNS($rdfNs, 'rdf:about', $languagePrefix . $code);
        $rdfNode->appendChild($descriptionNode);
        
        $typeNode = $doc->createElementNS($rdfNs, 'rdf:type');
        $typeNode->setAttributeNS($rdfNs, 'rdf:resource', $languageType);
        $descriptionNode->appendChild($typeNode);
        
        $labelNode = $doc->createElementNS($rdfsNs, 'rdfs:label');
        $labelNode->setAttributeNS($xmlNs, 'xml:lang', DEFAULT_LANG);
        $labelNode->appendChild($doc->createCDATASection($label));
        $descriptionNode->appendChild($labelNode);
        
        $valueNode = $doc->createElementNS($rdfNs, 'rdf:value');
        $valueNode->appendChild($doc->createCDATASection($code));
        $descriptionNode->appendChild($valueNode);
        
        $guiUsageNode = $doc->createElementNS($base, 'tao:languageUsages');
        $guiUsageNode->setAttributeNs($rdfNs, 'rdf:resource', INSTANCE_LANGUAGE_USAGE_GUI);
        $descriptionNode->appendChild($guiUsageNode);
        
        $dataUsageNode = $doc->createElementNS($base, 'tao:languageUsages');
        $dataUsageNode->setAttributeNs($rdfNs, 'rdf:resource', INSTANCE_LANGUAGE_USAGE_DATA);
        $descriptionNode->appendChild($dataUsageNode);
        
        $dataUsageNode = $doc->createElementNS($base, 'tao:LanguageOrientation');
        $dataUsageNode->setAttributeNs($rdfNs, 'rdf:resource', INSTANCE_ORIENTATION_LTR);
        $descriptionNode->appendChild($dataUsageNode);
        
        $returnValue = $doc;

        return $returnValue;
    }

}