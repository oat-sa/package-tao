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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\tao\model\search;

class Index extends \core_kernel_classes_Resource {
    
    public function getIdentifier()
    {
        return (string)$this->getUniquePropertyValue(new \core_kernel_classes_Property("http://www.tao.lu/Ontologies/TAO.rdf#IndexIdentifier"));
    }
    
    public function getTokenizer()
    {
        $tokenizerUri = $this->getUniquePropertyValue(new \core_kernel_classes_Property("http://www.tao.lu/Ontologies/TAO.rdf#IndexTokenizer"));
        $tokenizer = new \core_kernel_classes_Resource($tokenizerUri);
        $implClass = (string)$tokenizer->getUniquePropertyValue(new \core_kernel_classes_Property("http://www.tao.lu/Ontologies/TAO.rdf#TokenizerClass"));
        if (!class_exists($implClass)) {
            throw new \common_exception_Error('Tokenizer class "'.$implClass.'" not found for '.$tokenizer->getUri());
        }
        return new $implClass();
    }
    
    public function tokenize($value)
    {
        return $this->getTokenizer()->getStrings($value);
    }
    
    /**
     * Should the string matching be fuzzy
     * 
     * @return boolean
     */
    public function isFuzzyMatching()
    {
        $res = $this->getUniquePropertyValue(new \core_kernel_classes_Property("http://www.tao.lu/Ontologies/TAO.rdf#IndexFuzzyMatching"));
        return !is_null($res) && is_object($res) && $res->getUri() == GENERIS_TRUE;
    }
    
    /**
     * Should the value be stored
     * 
     * @return boolean
     */
    public function isStored()
    {
        return $this->getUri() === RDFS_LABEL;    
    }
}