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

namespace qtism\data\content\interactions;

use qtism\data\QtiComponentCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The end attempt interaction is a special type of interaction which allows 
 * item authors to provide the candidate with control over the way in which 
 * the candidate terminates an attempt. The candidate can use the interaction 
 * to terminate the attempt (triggering response processing) immediately, 
 * typically to request a hint. It must be bound to a response variable 
 * with base-type boolean and single cardinality.
 * 
 * If the candidate invokes response processing using an endAttemptInteraction 
 * then the associated response variable is set to true. If response processing 
 * is invoked in any other way, either through a different endAttemptInteraction 
 * or through the default method for the delivery engine, then the associated 
 * response variable is set to false. The default value of the response variable 
 * is always ignored.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class EndAttemptInteraction extends InlineInteraction {
    
    /**
     * From IMS QTI:
     * 
     * The string that should be displayed to the candidate as a prompt for 
     * ending the attempt using this interaction. This should be short, 
     * preferably one word. A typical value would be "Hint". For example, in 
     * a graphical environment it would be presented as the label on a button 
     * that, when pressed, ends the attempt.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $title;
    
    /**
     * 
     * @param string $responseIdentifier The identifier of the associated response variable.
     * @param unknown_type $title The title to be displayed to the candidate as a prompt for ending the attempt.
     * @param unknown_type $id The id of the bodyElement.
     * @param unknown_type $class The class of the bodyElement.
     * @param unknown_type $lang The language of the bodyElement.
     * @param unknown_type $label The label of the bodyElement.
     * @throws InvalidArgumentException If any of the argument is invalid.
     */
    public function __construct($responseIdentifier, $title, $id = '', $class = '', $lang = '', $label = '') {
        parent::__construct($responseIdentifier, $id, $class, $lang, $label);
        $this->setTitle($title);
    }
    
    /**
     * Set the title that will be displayed to the candidate as a prompt
     * for ending the attempt.
     * 
     * @param string $title A string.
     * @throws InvalidArgumentException If $title is not a string.
     */
    public function setTitle($title) {
        if (is_string($title) === true && empty($title) === false) {
            $this->title = $title;
        }
        else {
            $msg = "The 'title' argument must be a string, '" . gettype($title) . "' given.";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the title that will be displayed to the candidate as a prompt for
     * ending the attempt.
     * 
     * @return string A non-empty string.
     */
    public function getTitle() {
        return $this->title;
    }
    
    public function getComponents() {
        return new QtiComponentCollection();
    }
    
    public function getQtiClassName() {
        return 'endAttemptInteraction';
    }
}