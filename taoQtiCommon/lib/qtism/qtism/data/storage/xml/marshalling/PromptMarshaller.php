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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */


namespace qtism\data\storage\xml\marshalling;

use qtism\data\content\FlowStaticCollection;
use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \DOMElement;
use \InvalidArgumentException;

/**
 * The Marshaller implementation for Prompt elements of the content model.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PromptMarshaller extends ContentMarshaller {
    
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
            
            $fqClass = $this->lookupClass($element);
            $component = new $fqClass();
            
            $content = new FlowStaticCollection();
            $error = false;
            $exclusion = array('pre', 'hottext', 'printedVariable', 'templateBlock', 'templateInline', 'infoControl', 'feedbackBlock', 'rubricBlock', 'a', 'feedbackInline');
            
            foreach ($children as $c) {
                
                if (in_array($c->getQtiClassName(), $exclusion) === true) {
                    $error = true;
                    break;
                }
                
                try {
                    $content[] = $c;
                }
                catch (InvalidArgumentException $e) {
                    $error = true;
                    break;
                }
            }
            
            if ($error === true) {
                $qtiClass = $c->getQtiClassName();
                $msg = "A 'prompt' cannot contain '${qtiClass}' elements.";
                throw new UnmarshallingException($msg, $element);
            }
            
            $component->setContent($content);
            
            self::fillBodyElement($component, $element);
            
            return $component;
    }
    
    protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
        
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        self::fillElement($element, $component);
        
        foreach ($elements as $e) {
            $element->appendChild($e);
        }
        
        return $element;
    }
    
    protected function setLookupClasses() {
        $this->lookupClasses = array("qtism\\data\\content\\interactions");
    }
}