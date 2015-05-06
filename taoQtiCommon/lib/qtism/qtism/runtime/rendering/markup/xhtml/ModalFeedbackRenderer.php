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
use qtism\data\ShowHide;
use \DOMDocumentFragment;

/**
 * The renderer for ModalFeedback elements. Rendered elements
 * will get a 'qti-modalFeedback' additional CSS class and will be
 * transformed as 'div' elements.
 * 
 * It also takes care of producing the following x-data attributes.
 * 
 * * data-outcome-identifier = qti:modalFeedback->outcomeIdentifier
 * * data-show-hide = qti:modalFeedback->showHide
 * * data-identifier = qti:modalFeedback->identifier
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ModalFeedbackRenderer extends AbstractXhtmlRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('div');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-modalFeedback');
        
        $fragment->firstChild->setAttribute('data-outcome-identifier', $component->getOutcomeIdentifier());
        $fragment->firstChild->setAttribute('data-show-hide', ShowHide::getNameByConstant($component->getShowHide()));
        $fragment->firstChild->setAttribute('data-identifier', $component->getIdentifier());
        
        if ($this->getRenderingEngine()->getFeedbackShowHidePolicy() === AbstractMarkupRenderingEngine::CONTEXT_STATIC) {
            $this->additionalClass(($component->getShowHide() === ShowHide::SHOW) ? 'qti-hide' : 'qti-show');
        }
    }
}