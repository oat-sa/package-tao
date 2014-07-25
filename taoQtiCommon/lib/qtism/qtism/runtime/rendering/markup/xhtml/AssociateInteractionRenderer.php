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

use qtism\data\ShufflableCollection;
use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\QtiComponent;
use \DOMDocumentFragment;

/**
 * AssociateInteraction renderer. Rendered components will be transformed as 
 * 'div' elements with 'qti-blockInteraction' and 'qti-associateInteraction' additional CSS class.
 * 
 * The following data-X attributes will be rendered:
 * 
 * * data-responseIdentifier = qti:interaction->responseIdentifier
 * * data-shuffle = qti:associateInteraction->shuffle
 * * data-max-associations = qti:associateInteraction->maxAssociations
 * * data-min-associations = qti:associateInteraction->minAssociations
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssociateInteractionRenderer extends InteractionRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('div');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-blockInteraction');
        $this->additionalClass('qti-associateInteraction');
        
        $fragment->firstChild->setAttribute('data-shuffle', ($component->mustShuffle() === true) ? 'true' : 'false');
        $fragment->firstChild->setAttribute('data-max-associations', $component->getMaxAssociations());
        $fragment->firstChild->setAttribute('data-min-associations', $component->getMinAssociations());
    }
    
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendChildren($fragment, $component, $base);
        
        if ($this->getRenderingEngine()->mustShuffle() === true) {
            Utils::shuffle($fragment->firstChild, new ShufflableCollection($component->getSimpleAssociableChoices()->getArrayCopy()));
        }
        
        // The number of possible associations to display is maxAssociations if the attribute is present and different from 0, otherwise:
        // 
        // * minAssociations, if different from 0 is used to determine the possible associations to display. Otherwise,
        // * a single possible association is displayed. Actions to undertake when this first association is done by the candidate depends on the implementation.
        $nbAssoc = (($assoc = $component->getMaxAssociations()) > 0) ? $assoc : ((($assoc = $component->getMinAssociations()) > 0) ? $assoc : 1);
        
        for ($i = 0; $i < $nbAssoc; $i++) {
            $associationElt = $fragment->ownerDocument->createElement('div');
            $associationElt->setAttribute('class', 'qti-association');
            
            // A container for the first selected option...
            $firstElt = $fragment->ownerDocument->createElement('span');
            $firstElt->setAttribute('class', 'qti-association-first');
            $associationElt->appendChild($firstElt);
            
            // And a second container for the second selected option.
            $secondElt = $fragment->ownerDocument->createElement('span');
            $secondElt->setAttribute('class', 'qti-association-second');
            $associationElt->appendChild($secondElt);
            
            $fragment->firstChild->appendChild($associationElt);
        }
    } 
}