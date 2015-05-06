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
use qtism\data\content\Block;
use qtism\data\content\Flow;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * An interaction that behaves like a block in the content model.
 * Most interactions are of this type.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class BlockInteraction extends Interaction implements Block, Flow {
	
	/**
     * The base URI of the BlockInteraction.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $xmlBase = '';
	
	/**
	 * From IMS QTI:
	 * 
	 * An optional prompt for the interaction.
	 * 
	 * @var Prompt
	 * @qtism-bean-property
	 */
	private $prompt = null;
	
	/**
	 * Create a new BlockInteraction object.
	 * 
	 * @param string $responseIdentifier The identifier of the associated response.
	 * @param string $id The id of the bodyElement.
	 * @param string $class The class of the bodyElement.
	 * @param string $lang The language of the bodyElement.
	 * @param string $label The label of the bodyElement.
	 * @throws InvalidArgumentException If one of the argument is invalid.
	 */
	public function __construct($responseIdentifier, $id = '', $class = '', $lang = '', $label = '') {
	    parent::__construct($responseIdentifier, $id, $class, $lang, $label);
	}
	
	/**
	 * Get the prompt for the interaction.
	 * 
	 * @return Prompt
	 */
	public function getPrompt() {
		return $this->prompt;
	}
	
	/**
	 * Set the prompt for the interaction.
	 * 
	 * @param Prompt $prompt
	 */
	public function setPrompt($prompt = null) {
		$this->prompt = $prompt;
	}
	
	/**
	 * Whether the BlockInteraction has a prompt.
	 * 
	 * @return boolean
	 */
	public function hasPrompt() {
	    return $this->getPrompt() !== null;
	}
	
    /**
     * Set the base URI of the BlockInteraction.
     *
     * @param string $xmlBase A URI.
     * @throws InvalidArgumentException if $base is not a valid URI nor an empty string.
     */
    public function setXmlBase($xmlBase = '') {
        if (is_string($xmlBase) && (empty($xmlBase) || Format::isUri($xmlBase))) {
            $this->xmlBase = $xmlBase;
        }
        else {
            $msg = "The 'xmlBase' argument must be an empty string or a valid URI, '" . $xmlBase . "' given";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the base URI of the BlockInteraction.
     *
     * @return string An empty string or a URI.
     */
    public function getXmlBase() {
        return $this->xmlBase;
    }
    
    public function hasXmlBase() {
        return $this->getXmlBase() !== '';
    }
    
    public function getComponents() {
        
        $array = array();
        if ($this->hasPrompt() === true) {
            $array[] = $this->getPrompt();
        }
        
        return new QtiComponentCollection($array);
    }
}