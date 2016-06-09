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
use qtism\data\content\interactions\Orientation;
use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\QtiComponent;
use \DOMDocumentFragment;

/**
 * OrderInteraction renderer. Rendered components will be transformed as 
 * 'div' elements with a 'qti-orderInteraction' additional CSS class.
 * 
 * The following data-X attributes will be rendered:
 * 
 * * data-shuffle = qti:orderInteraction->shuffle
 * * data-max-choices = qti:orderInteraction->maxChoices (only if specified in QTI-XML representation)
 * * data-min-choices = qti:orderInteraction->minChoices (only if specified in QTI-XML representation)
 * * data-orientation = qti:orderInteraction->orientation
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OrderInteractionRenderer extends InteractionRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('div');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-orderInteraction');
        $this->additionalClass(($component->getOrientation() === Orientation::VERTICAL) ? 'qti-vertical' : 'qti-horizontal');
        
        $fragment->firstChild->setAttribute('data-shuffle', ($component->mustShuffle() === true) ? 'true' : 'false');
        
        if ($component->hasMaxChoices() === true) {
            $fragment->firstChild->setAttribute('data-max-choices', $component->getMaxChoices());
        }
        else {
            $fragment->firstChild->setAttribute('data-min-choices', $component->getMinChoices());
        }
        
        $fragment->firstChild->setAttribute('data-orientation', ($component->getOrientation() === Orientation::VERTICAL) ? 'vertical' : 'horizontal');
    }
    
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendChildren($fragment, $component, $base);
        
        if ($this->getRenderingEngine()->mustShuffle() === true) {
            Utils::shuffle($fragment->firstChild, new ShufflableCollection($component->getSimpleChoices()->getArrayCopy()));
        }
    }
}