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
namespace oat\tao\model\search\zend;

use oat\tao\model\search\Search;
use tao_models_classes_FileSourceService;
use common_Logger;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Search\QueryHit;
use oat\tao\model\search\Index;

/**
 * Zend Index helper 
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class ZendIndexer
{	
    private $resource;
    
    public function __construct(\core_kernel_classes_Resource $resource)
    {
        $this->resource = $resource;
    }
    
    public function toDocument()
    {
        $document = new Document();
//        common_Logger::i('indexing '.$this->resource->getLabel());
        
        $this->addUri($document);
        $this->indexTypes($document);
        foreach ($this->getIndexedProperties() as $property) {
            $this->indexProperty($document, $property);
        }
        
        return $document;
    }
    
    /**
     * Store uri, don't index it
     * 
     * @param Document $document
     */
    protected function addUri(Document $document)
    {
        $document->addField(Document\Field::unIndexed('uri', $this->resource->getUri()));
    }
    
    /**
     * @param Document $document
     */
    protected function indexTypes(Document $document)
    {
        $toDo = array();
        foreach ($this->resource->getTypes() as $class) {
            $toDo[] = $class->getUri();
            $document->addField(Document\Field::Text('class', $class->getLabel()));
        }
        
        $done = array(RDFS_CLASS, TAO_OBJECT_CLASS);
        $toDo = array_diff($toDo, $done);
        
        $classLabels = array();
        while (!empty($toDo)) {
            $class = new \core_kernel_classes_Class(array_pop($toDo));
            $classLabels[] = $class->getLabel();
            foreach ($class->getParentClasses() as $parent) {
                if (!in_array($parent->getUri(), $done)) {
                    $toDo[] = $parent->getUri();
                }
            }
            $done[] = $class->getUri();
        }
        $field = Document\Field::Keyword('class_r', $classLabels);
        $field->isStored = false;
        $document->addField($field);
    }
    
    protected function indexProperty(Document $document, \core_kernel_classes_Property $property)
    {
        $indexes = $property->getPropertyValues(new \core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAO.rdf#PropertyIndex'));
        foreach ($indexes as $indexUri) {
            $index = new Index($indexUri);
            $id = $index->getIdentifier();
            $strings = $index->tokenize($this->resource->getPropertyValues($property));
            
            if (!empty($strings)) {
                
                if ($index->isFuzzyMatching()) {
                    // cannot store multiple fuzzy strings
                    $string = implode(' ', $strings);
                    $field = Document\Field::Text($index->getIdentifier(), $string);
                    $field->isStored = $index->isStored();
                    $document->addField($field);
                } else {
                    $value = count($strings) > 1 ? $strings : reset($strings);
                    $field = Document\Field::Keyword($index->getIdentifier(), $value);
                    $field->isStored = $index->isStored() && !is_array($value); // storage of arrays not supported
                    $document->addField($field);
                }
            }
        }
    }
    
    protected function getIndexedProperties()
    {
        $classProperties = array(new \core_kernel_classes_Property(RDFS_LABEL));
        foreach ($this->resource->getTypes() as $type) {
            $classProperties = array_merge($classProperties, \tao_helpers_form_GenerisFormFactory::getClassProperties($type));
        }
    
        return $classProperties;
    }

}