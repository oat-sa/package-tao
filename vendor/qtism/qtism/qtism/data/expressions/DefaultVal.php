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


namespace qtism\data\expressions;

use \InvalidArgumentException;

/**
 * Warning: This class is named DefaultVal instead of Default (name in QTI) because
 * of the reserved word 'default' of PHP.
 * 
 * From IMS QTI:
 * 
 * This expression looks up the declaration of an itemVariable and returns the associated 
 * defaultValue or NULL if no default value was declared. When used in outcomes processing 
 * item identifier prefixing (see variable) may be used to obtain the default value from an 
 * individual item.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @link http://www.php.net/manual/en/reserved.keywords.php
 */
class DefaultVal extends Expression {
	
	/**
	 * The QTI Identifier of the variable you want the default value.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $identifier;
	
	/**
	 * Create a new instance of DefaultValue.
	 * 
	 * @param string $identifier A QTI Identifier.
	 * @throws InvalidArgumentException If $identifier is not a valid QTI Identifier.
	 */
	public function __construct($identifier) {
		$this->setIdentifier($identifier);
	}
	
	/**
	 * Set the identifier of the variable you want the default value.
	 * 
	 * @param string $identifier A QTI Identifier.
	 * @throws InvalidArgumentException If $identifier is not a valid QTI Identifier.
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}
	
	/**
	 * Get the identifier of the variable you want the default value.
	 * 
	 * @return string A QTI Identifier.
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
	
	public function getQtiClassName() {
		return 'default';
	}
}
