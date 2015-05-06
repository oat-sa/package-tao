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
 * TextEntryInteraction renderer. Will render components
 * as 'input' elements with type 'text' and an additional classes
 * of 'qti-textEntryInteraction' and 'qti-inlineInteraction'.
 * 
 * The following data-X attributes will be rendered:
 * 
 * * data-response-identifier = qti:interaction->responseIdentifier
 * * data-base = qti:stringInteraction->base
 * * data-string-identifier = qti:stringInteraction->stringIdentifier (only if set in QTI-XML counter-part).
 * * data-expected-length = qti:stringInteraction->expectedLength (only if set in QTI-XML counter-part).
 * * data-pattern-mask = qti:stringInteraction->patternMask (only if set in QTI-XML counter-part).
 * * data-placeholder-text = qti:stringInteraction->placeholderText (only if set in QTI-XML counter-part).
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TextEntryInteractionRenderer extends StringInteractionRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('input');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-inlineInteraction');
        $this->additionalClass('qti-textEntryInteraction');
        $fragment->firstChild->setAttribute('type', 'text');
    }
}