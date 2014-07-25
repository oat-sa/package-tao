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

use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\QtiComponent;
use \DOMDocumentFragment;

/**
 * DrawingInteraction renderer. Rendered components will be transformed as 
 * 'div' elements with the 'qti-blockInteraction' and 'qti-drawingInteraction' additional CSS class.
 * 
 * * The <object> element representing the background image of the drawing canvas is transformed into an <img> tag.
 * * A <canvas> element representing the place the candidate can draw is appended to the drawing interaction container.
 * 
 * If the former <object> tag had 'width' or 'height' attribute values, they will be transferred to the newly created
 * <img> and <canvas> elements.
 * 
 * The following data-X attributes will be rendered:
 * 
 * * data-responseIdentifier = qti:interaction->responseIdentifier
 * 
 * It is the responsibility of the client of the rendering engine to choose a policy for the final
 * behaviour of the output. It can choose to position the <img> element in the back of the <canvas>
 * element OR, remove the <img> element and draw its content in the canvas by using the canvas.drawImage
 * method.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class DrawingInteractionRenderer extends InteractionRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('div');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-blockInteraction');
        $this->additionalClass('qti-drawingInteraction');
    }
    
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendChildren($fragment, $component, $base);
        
        $width = null;
        $height = null;
        
        if ($component->getObject()->hasWidth() === true) {
            $width = $component->getObject()->getWidth();
        }
        
        if ($component->getObject()->hasHeight() === true) {
            $height = $component->getObject()->getHeight();
        }
        
        $imgElt = $fragment->ownerDocument->createElement('img');
        $imgElt->setAttribute('src', $component->getObject()->getData());
        
        // Replace <object> by <img>.
        $objectElt = $fragment->firstChild->getElementsByTagName('object')->item(0);
        $fragment->firstChild->replaceChild($imgElt, $objectElt);
        
        // Append a <canvas>.
        $canvasElt = $fragment->ownerDocument->createElement('canvas');
        $fragment->firstChild->appendChild($canvasElt);
        
        if (empty($width) === false) {
            $imgElt->setAttribute('width', $width);
            $canvasElt->setAttribute('width', $width);
        }
        
        if (empty($height) === false) {
            $imgElt->setAttribute('height', $height);
            $canvasElt->setAttribute('height', $height);
        }
    }
}