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

use qtism\data\storage\xml\Utils as XmlUtils;
use qtism\data\storage\xml\marshalling\Marshaller;
use qtism\data\ShufflableCollection;
use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\data\QtiComponent;
use \DOMDocumentFragment;

/**
 * MatchInteraction renderer. Rendered components will be transformed as 
 * 'div' elements with the 'qti-blockInteraction' and 'qti-matchInteraction'
 * additional CSS class.
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
class MatchInteractionRenderer extends InteractionRenderer {
    
    public function __construct(AbstractMarkupRenderingEngine $renderingEngine = null) {
        parent::__construct($renderingEngine);
        $this->transform('div');
    }
    
    protected function appendAttributes(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        
        parent::appendAttributes($fragment, $component, $base);
        $this->additionalClass('qti-blockInteraction');
        $this->additionalClass('qti-matchInteraction');
        
        $fragment->firstChild->setAttribute('data-shuffle', ($component->mustShuffle() === true) ? 'true' : 'false');
        $fragment->firstChild->setAttribute('data-max-associations', $component->getMaxAssociations());
    	$fragment->firstChild->setAttribute('data-min-associations', $component->getMinAssociations());
    }
    
    protected function appendChildren(DOMDocumentFragment $fragment, QtiComponent $component, $base = '') {
        parent::appendChildren($fragment, $component, $base);
        
        // Retrieve the two rendered simpleMatchSets and shuffle if needed.
        $currentSet = 0;
        $choiceElts = array();
        $simpleMatchSetElts = array();
        
        for ($i = 0; $i < $fragment->firstChild->childNodes->length; $i++) {
        	$n = $fragment->firstChild->childNodes->item($i);
        	if (Utils::hasClass($n, 'qti-simpleMatchSet') === true) {
        		$simpleMatchSetElts[] = $n;
            	$sets = $component->getSimpleMatchSets();
                    
            	if ($this->getRenderingEngine()->mustShuffle() === true) {
                	Utils::shuffle($n, new ShufflableCollection($sets[$currentSet]->getSimpleAssociableChoices()->getArrayCopy()));
            	}
            	
            	// Retrieve the two content of the two simpleMatchSets, separately.
            	$choiceElts[] = Marshaller::getChildElementsByTagName($n, 'div');
                $currentSet++;
            }
        }
        
        // simpleMatchSet class cannot be rendered into a table :/
        foreach ($simpleMatchSetElts as $sms) {
        	$fragment->firstChild->removeChild($sms);
        }
        
        $table = $fragment->ownerDocument->createElement('table');
        $fragment->firstChild->appendChild($table);
        
        // Build the table header.
        $tr = $fragment->ownerDocument->createElement('tr');
        $table->appendChild($tr);
        // Empty upper left cell.
        $tr->appendChild($fragment->ownerDocument->createElement('th'));
        
        for ($i = 0; $i < count($choiceElts[1]); $i++) {
        	$tr->appendChild(XmlUtils::changeElementName($choiceElts[1][$i], 'th'));
        }
        
        // Build all remaining rows.
        for ($i = 0; $i < count($choiceElts[0]); $i++) {
        	$tr = $fragment->ownerDocument->createElement('tr');
        	$tr->appendChild(XmlUtils::changeElementName($choiceElts[0][$i], 'th'));
        	$table->appendChild($tr);
        	
        	for ($j = 0; $j < count($choiceElts[1]); $j++) {
        		$input = $fragment->ownerDocument->createElement('input');
        		$input->setAttribute('type', 'checkbox');
        		$td = $fragment->ownerDocument->createElement('td');
        		$td->appendChild($input);
        		$tr->appendChild($td);
        	}
        }
    }
}