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

namespace qtism\runtime\rendering\markup\xhtml;

use qtism\data\content\Stylesheet;
use qtism\runtime\rendering\RenderingException;
use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\QtiComponent;
use qtism\runtime\rendering\markup\AbstractMarkupRenderer;
use \DOMDocumentFragment;

/**
 * Base class of all XHTML renderers.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class AbstractXhtmlRenderer extends AbstractMarkupRenderer {
    
    /**
     * A tag name to be used instead of the 
     * QTI class name for rendering.
     * 
     * @var string
     */
    private $replacementTagName = '';
    
    /**
     * A set of additional CSS classes to be added
     * to the rendered element.
     *
     * @var array
     */
    private $additionalClasses = array();
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
    }
    
    /**
     * Render a QtiComponent into a DOMDocumentFragment that will be registered
     * in the current rendering context.
     * 
     * @return DOMDocumentFragment A DOMDocumentFragment object containing the rendered $component into another constitution with its children rendering appended.
     * @throws RenderingException If an error occurs while rendering $component.
     */
    public function render($component, $base = '') {
        $renderingEngine = $this->getRenderingEngine();
        $doc = $renderingEngine->getDocument();
        $fragment = $doc->createDocumentFragment();
        
        // Apply rendering...
        $this->renderingImplementation($fragment, $component, $base);
        
        // Specific case for stylesheet elements...
        if ($component instanceof Stylesheet && $renderingEngine->getStylesheetPolicy() === AbstractMarkupRenderingEngine::STYLESHEET_SEPARATE) {
            // Stylesheet must be rendered separately.
            $stylesheets = $renderingEngine->getStylesheets();
            
            if ($stylesheets->childNodes->length === 0) {
                $stylesheets->appendChild($fragment);
            }
            else {
                $stylesheets->insertBefore($fragment, $stylesheets->firstChild);
            }
        }
        else {
            // Stylesheet must be rendered at the same place.
            $renderingEngine->storeRendering($component, $fragment);
        }
        
        return $fragment;
    }
    
    protected function renderingImplementation(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        
        $this->appendElement($fragment, $component, $base);
        $this->appendChildren($fragment, $component, $base);
        $this->appendAttributes($fragment, $component, $base);
        
        
        if ($this->hasAdditionalClasses() === true) {
            $classes = implode("\x20", $this->getAdditionalClasses());
            $currentClasses = $fragment->firstChild->getAttribute('class');
            $glue = ($currentClasses !== '') ? "\x20" : "";
            $fragment->firstChild->setAttribute('class', $currentClasses . $glue . $classes);
        }
        
        // Reset additional classes for next rendering.
        $this->setAdditionalClasses(array());
    }
    
    /**
     * Append a new DOMElement to the currently rendered $fragment which is suitable
     * to $component.
     * 
     * @param DOMDocumentFragment $fragment
     * @param QtiComponent $component
     */
    protected function appendElement(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        $tagName = ($this->hasReplacementTagName() === true) ? $this->getReplacementTagName() : $component->getQtiClassName();
        $fragment->appendChild($this->getRenderingEngine()->getDocument()->createElement($tagName));
    }
    
    /**
     * Append the children renderings of $components to the currently rendered $fragment.
     * 
     * @param DOMDocumentFragment $fragment
     * @param QtiComponent $component
     */
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        foreach ($this->getRenderingEngine()->getChildrenRenderings($component) as $childrenRendering) {
            $fragment->firstChild->appendChild($childrenRendering);
        }
    }
    
    /**
     * Append the necessary attributes of $component to the currently rendered $fragment.
     * 
     * @param DOMDocumentFragment $fragment
     * @param QtiComponent $component
     */
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        $this->handleXmlBase($component, $fragment->firstChild);
    }
    
    /**
     * Set the replacement tag name.
     * 
     * @param string $replacementTagName
     */
    protected function setReplacementTagName($replacementTagName) {
        $this->replacementTagName = $replacementTagName;
    }
    
    /**
     * Get the replacement tag name.
     * 
     * @return string
     */
    protected function getReplacementTagName() {
        return $this->replacementTagName;
    }
    
    /**
     * Whether a replacement tag name is defined.
     * 
     * @return boolean
     */
    protected function hasReplacementTagName() {
        return $this->getReplacementTagName() !== '';
    }
    
    /**
     * The renderer will by default render the QTI Component into its markup equivalent, using
     * the QTI class name returned by the component as the rendered element name.
     * 
     * Calling this method will make the renderer use $tagName as the element node name to be
     * used at rendering time.
     * 
     * @param string $tagName A tagname e.g. 'div'.
     */
    public function transform($tagName) {
        $this->setReplacementTagName($tagName);
    }
    
    /**
     * Set the array of additional CSS classes.
     *
     * @param array $additionalClasses
     */
    protected function setAdditionalClasses(array $additionalClasses) {
        $this->additionalClasses = $additionalClasses;
    }
    
    /**
     * Get the array of additional CSS classes.
     *
     * @return array
     */
    protected function getAdditionalClasses() {
        return $this->additionalClasses;
    }
    
    /**
     * Whether additional CSS classes are defined for rendering.
     *
     * @return boolean
     */
    protected function hasAdditionalClasses() {
        return count($this->getAdditionalClasses()) > 0;
    }
    
    /**
     * Add an additional CSS class to be rendered.
     *
     * @param string $additionalClass A CSS class.
     */
    public function additionalClass($additionalClass) {
        $additionalClasses = $this->getAdditionalClasses();
        $additionalClasses[] = $additionalClass;
        $this->setAdditionalClasses(array_unique($additionalClasses));
    }
}