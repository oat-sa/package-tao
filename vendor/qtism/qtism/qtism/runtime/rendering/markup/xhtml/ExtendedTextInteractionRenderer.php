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

use qtism\data\content\interactions\TextFormat;
use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\QtiComponent;
use \DOMDocumentFragment;

/**
 * ExtendedTextInteraction renderer. Will render components
 * as 'div' elements with type 'text' and the additional classes
 * of 'qti-extendedTextInteraction' and 'qti-blockInteraction'.
 * 
 * The generated 'div' element will be composed of:
 * * A 'div' element with the additional 'qti-prompt' CSS class if a prompt is present in the interaction.
 * * A 'textarea' element representing the text input.
 * 
 * The following data-X attributes will be rendered:
 * 
 * * data-response-identifier = qti:interaction->responseIdentifier
 * * data-base = qti:stringInteraction->base
 * * data-string-identifier = qti:stringInteraction->stringIdentifier (only if set in QTI-XML counter-part).
 * * data-expectedLength = qti:stringInteraction->expectedLength (only if set in QTI-XML counter-part).
 * * data-pattern-mask = qti:stringInteraction->patternMask (only if set in QTI-XML counter-part).
 * * data-placeholder-text = qti:stringInteraction->placeholderText (only if set in QTI-XML counter-part).
 * * data-max-strings = qti:extendedTextInteraction->maxStrings (only if set in QTI-XML counter-part).
 * * data-min-strings = qti:extendedTextInteraction->minStrings.
 * * data-expected-lines = qti:extendedTextInteraction->expectedLines (only if set in QTI-XML counter-part).
 * * data-format = qti:extendedTextInteraction->format.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ExtendedTextInteractionRenderer extends StringInteractionRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('div');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-blockInteraction');
        $this->additionalClass('qti-extendedTextInteraction');
        
        $fragment->firstChild->setAttribute('data-min-strings', $component->getMinStrings());
        $fragment->firstChild->setAttribute('data-format', TextFormat::getNameByConstant($component->getFormat()));
        
        if ($component->hasMaxStrings() === true) {
            $fragment->firstChild->setAttribute('data-max-strings', $component->getMaxStrings());
        }
        
        if ($component->hasExpectedLines() === true) {
            $fragment->firstChild->setAttribute('data-expected-lines', $component->getExpectedLines());
        }
    }
    
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendChildren($fragment, $component, $base);
        
        // Append a textarea...
        $textarea = $fragment->ownerDocument->createElement('textarea');
        // Makes the textarea non self-closing...
        $textarea->appendChild($fragment->ownerDocument->createTextNode(''));
        $fragment->firstChild->appendChild($textarea);
    }
}