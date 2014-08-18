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
 * GapMatchInteraction renderer. Rendered components will be transformed as 
 * 'div' elements with the 'qti-blockInteraction' and 'qti-gapMatchInteraction' additional CSS classes.
 * 
 * The following data-X attributes will be rendered:
 * 
 * * data-response-identifier = qti:interaction->responseIdentifier
 * * data-shuffle = qti:gapMatchInteraction->shuffle
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class GapMatchInteractionRenderer extends InteractionRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('div');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-blockInteraction');
        $this->additionalClass('qti-gapMatchInteraction');
        
        $fragment->firstChild->setAttribute('data-shuffle', ($component->mustShuffle() === true) ? 'true' : 'false');
    }
    
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendChildren($fragment, $component, $base);
        
        if ($this->getRenderingEngine()->mustShuffle() === true) {
            Utils::shuffle($fragment->firstChild, new ShufflableCollection($component->getGapChoices()->getArrayCopy()));
        }
    }
}