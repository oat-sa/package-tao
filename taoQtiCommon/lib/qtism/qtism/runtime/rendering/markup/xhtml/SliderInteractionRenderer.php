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

use qtism\data\content\interactions\Orientation;
use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\QtiComponent;
use \DOMDocumentFragment;

/**
 * SliderInteraction renderer. Rendered components will be transformed as 
 * 'div' elements with the 'qti-sliderInteraction' and 'qti-blockInteraction' additional CSS classes.
 * 
 * An additional 'qti-horizontal' or 'qti-vertical' CSS class is also
 * added depending on the value of qti:sliderInteraction->orientation.
 * 
 * A decorative <div> element with addictionnal CSS class 'qti-slider' is appended
 * to the element representing the 'qti-sliderInteraction', in order to represent and place
 * the widget element to be replaced by a slider implementation at runtime.
 * 
 * The following data-X attributes will be rendered:
 * 
 * * data-response-identifier = qti:interaction->responseIdentifier
 * * data-lower-bound = qti:sliderInteraction->lowerBound
 * * data-upper-bound = qti:sliderInteraction->upperBound
 * * data-step = qti:sliderInteraction->step (Only if a value is present in QTI-XML)
 * * data-step-label = qti:sliderInteraction->stepLabel
 * * data-orientation = qti:sliderInteraction->orientation 
 * * data-reverse = qti:sliderInteraction->reverse
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SliderInteractionRenderer extends InteractionRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('div');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-blockInteraction');
        $this->additionalClass('qti-sliderInteraction');
        $this->additionalClass(($component->getOrientation() === Orientation::HORIZONTAL) ? 'qti-horizontal' : 'qti-vertical');
        
        $fragment->firstChild->setAttribute('data-lower-bound', $component->getLowerBound());
        $fragment->firstChild->setAttribute('data-upper-bound', $component->getUpperBound());
        $fragment->firstChild->setAttribute('data-step-label', ($component->mustStepLabel() === true) ? 'true' : 'false');
        $fragment->firstChild->setAttribute('data-orientation', ($component->getOrientation() === Orientation::VERTICAL) ? 'vertical' : 'horizontal');
        $fragment->firstChild->setAttribute('data-reverse', ($component->mustReverse() === true) ? 'true' : 'false');
        
        if ($component->hasStep() === true) {
            $fragment->firstChild->setAttribute('data-step', $component->getStep());
        }
    }
    
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendChildren($fragment, $component, $base);
        
        // Insert an element representing the slider 'widget' itself.
        $sliderElt = $fragment->firstChild->ownerDocument->createElement('div');
        $sliderElt->setAttribute('class', 'qti-slider');
        $fragment->firstChild->appendChild($sliderElt);
    }
}