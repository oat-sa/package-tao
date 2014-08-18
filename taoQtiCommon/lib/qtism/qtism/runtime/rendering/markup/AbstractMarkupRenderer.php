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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */

namespace qtism\runtime\rendering\markup;

use qtism\runtime\rendering\Renderable;
use qtism\data\content\Flow;
use qtism\common\utils\Url;
use qtism\data\QtiComponent;
use \DOMNode;
use \DOMElement;
use \InvalidArgumentException;
use \ReflectionObject;

/**
 * Interface to implement to pretend to be a class able
 * to render a QtiComponent into another consitution such as
 * XHTML, HTML5, Canvas, ...
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class AbstractMarkupRenderer implements Renderable {
    
    private $renderingEngine;
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        $this->setRenderingEngine($renderingEngine);
    }
    public function setRenderingEngine(AbstractMarkupRenderingEngine $renderingEngine = null) {
        $this->renderingEngine = $renderingEngine;
    }
    
    /**
     * Get the rendering engine currently driving the rendering
     * process.
     * 
     * @return AbstractMarkupRenderingEngine
     */
    public function getRenderingEngine() {
        return $this->renderingEngine;
    }
    
    /**
     * Transform a URL depending on a given $baseUrl and how the 
     * rendering engine is considering them.
     * 
     * @param string $url A given URL (Uniform Resource Locator).
     * @param string $baseUrl a baseUrl (xml:base).
     * @return string A transformed URL.
     */
    protected function transformUri($url, $baseUrl) {
        // Only relative URIs must be transformed while
        // taking xml:base into account.
        if (Url::isRelative($url) === false) {
            return $url;
        }
        
        $xmlBasePolicy = $this->getRenderingEngine()->getXmlBasePolicy();
        
        switch ($xmlBasePolicy) {
            
            case AbstractMarkupRenderingEngine::XMLBASE_PROCESS:
                if (empty($baseUrl) === false) {
                    return Url::rtrim($baseUrl) . '/' . Url::ltrim($url);
                }
                else {
                    return $url;
                }
            break;
            
            default:
                return $url;
            break;
        }
    }
    
    protected function handleXmlBase(QtiComponent $component, DOMNode $node) {
        if ($node instanceof DOMElement && 
            $this->getRenderingEngine()->getXmlBasePolicy() === AbstractMarkupRenderingEngine::XMLBASE_KEEP &&
            $component instanceof Flow &&
            $component->hasXmlBase()) {
            
            $node->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'base', $component->getXmlBase());
        }
    }
}