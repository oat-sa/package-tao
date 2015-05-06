<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\data\content;

use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * A feedback element that forms part of a Non-adaptive Item must not contain an 
 * interaction object, either directly or indirectly.
 * 
 * When an interaction is contained in a hidden feedback element it must also 
 * be hidden. The candidate must not be able to set or update the value of the 
 * associated response variables.
 * 
 * Feedback elements can be embedded inside each other, with one exception: feedBackInline
 * cannot contain feedbackBlock elements.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
interface FeedbackElement {
    
    /**
     * Set the identifier of an outcome variable that determines
     * the visibility of the feedbackElement.
     * 
     * @param string $outcomeIdentifier A QTI Identifier.
     * @throws InvalidArgumentException If $outcomeIdentifier is not a valid QTI identifier.
     */
    public function setOutcomeIdentifier($outcomeIdentifier);
    
    /**
     * Get the identifier of an outcome variable that determines
     * the visibility of the feedback element.
     * 
     * @return string A QTI Identifier.
     */
    public function getOutcomeIdentifier();
    
    /**
     * Set how the visibility of the feedbackElement is controlled.
     * 
     * @param integer $showHide A value from the ShowHide enumeration.
     * @throws InvalidArgumentException If $showHide is not a value from the ShowHide enumeration.
     */
    public function setShowHide($showHide);
    
    /**
     * Get how the visibility of the feedbackElement is controlled.
     * 
     * @return integer A value from the ShowHide enumeration.
     */
    public function getShowHide();
    
    /**
     * Set the identifier typed value that determines the visibility of the feedback in conjunction
     * with the showHide attribute.
     * 
     * @param string $identifier A QTI identifier.
     * @throws InvalidArgumentException If $identifier is not a valid QTI identifier.
     */
    public function setIdentifier($identifier);
    
    /**
     * Get the identifier typed value that determines the visibility of the feedback in conjunction
     * with the showHide attribute.
     * 
     * @return string A QTI identifier.
     */
    public function getIdentifier();
}