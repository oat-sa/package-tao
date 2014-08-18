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
 * EndAttemptInteraction renderer. Rendered components will be transformed as 
 * 'span' elements with the 'qti-inlineInteraction' and 'qti-endAttemptInteraction' additional CSS class.
 * 
 * An <input type="submit"> element is appended to the rendered element to depict
 * the input to be pressed by the candidate to end the attempt. The attribute 'value' of
 * the <input type="submit"> element will be set with the value of qti:endAttemptInteraction->title.
 * 
 * The following data-X attributes will be rendered:
 * 
 * * data-response-identifier = qti:interaction->responseIdentifier
 * * data-title = qti:endAttemptInteraction->title
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class EndAttemptInteractionRenderer extends InteractionRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('span');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-inlineInteraction');
        $this->additionalClass('qti-endAttemptInteraction');
        $fragment->firstChild->setAttribute('data-title', $component->getTitle());
    }
    
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendChildren($fragment, $component, $base);
        
        $submitElt = $fragment->ownerDocument->createElement('input');
        $submitElt->setAttribute('type', 'submit');
        $submitElt->setAttribute('value', $component->getTitle());
        $fragment->firstChild->appendChild($submitElt);
    }
}