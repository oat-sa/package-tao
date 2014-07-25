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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data;

use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * Section rubric is presented to the candidate with each item contained (directly or indirectly) 
 * by the section. As sections are nestable the rubric presented for each item is the 
 * concatenation of the rubric blocks from the top-most section down to the item's 
 * immediately enclosing section.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RubricBlock extends QtiComponent {
	
	/**
	 * The views in which the rubric block's content are to be shown.
	 * 
	 * @var ViewCollection
	 */
	private $views;
	
	/**
	 * The purpose for which the rubric is intended to be used.
	 * 
	 * @var string
	 */
	private $use = '';
	
	/**
	 * The stylesheets are used to format just the contents of the rubricBlock.
	 * 
	 * @var StyleSheetCollection
	 */
	private $stylesheets;
	
	/**
	 * The content of the rubrick block as a string.
	 * 
	 * @var string
	 */
	private $content = '';
	
	/**
	 * Create a new instance of RubricBlock.
	 * 
	 * @param ViewCollection $views The views in which the rubric block's content are to be shown.
	 * @param string $use he purpose for which the rubric is intended to be used.
	 * @throws InvalidArgumentException If $use is not a string or $
	 */
	public function __construct(ViewCollection $views, $use = '') {
		$this->setViews($views);
		$this->setUse($use);
		$this->setStylesheets(new StylesheetCollection());
	}
	
	/**
	 * Get the views in which the rubric block's content are to be shown.
	 * 
	 * @return ViewCollection A collection of values that belong to the View enumeration.
	 */
	public function getViews() {
		return $this->views;
	}
	
	/**
	 * Set the views in which the rubric block's content are to be shown.
	 * 
	 * @param ViewCollection $views A collection of values that belong to the View enumeration.
	 * @throws InvalidArgumentException If $views is an empty collection.
	 */
	public function setViews(ViewCollection $views) {
		if (count($views) > 0) {
			$this->views = $views;
		}
		else {
			$msg = "A RubricBlock object must contain at least one View.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get he purpose for which the rubric is intended to be used. If there is no
	 * use for the Rubric Block, an empty string is returned.
	 * 
	 * @return string The use or an empty string ('').
	 */
	public function getUse() {
		return $this->use;
	}
	
	/**
	 * Set he purpose for which the rubric is intended to be used. If there is no
	 * use for the Rubric Block.
	 * 
	 * @param string $use A use.
	 * @throws InvalidArgumentException If $use is not a string.
	 */
	public function setUse($use) {
		if (gettype($use) === 'string') {
			$this->use = $use;
		}
		else {
			$msg = "The use argument must be a string, '" . gettype($use) . "' given";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the stylesheets to format the contents of the Rubric Block.
	 * 
	 * @return StylesheetCollection A collection of stylesheet references.
	 */
	public function getStylesheets() {
		return $this->stylesheets;
	}
	
	/**
	 * Set the stylesheets to format the contents of the Rubric Block.
	 * 
	 * @param StylesheetCollection $stylesheets A collection of stylesheet references.
	 */
	public function setStylesheets(StylesheetCollection $stylesheets) {
		$this->stylesheets = $stylesheets;
	}
	
	/**
	 * Set the content of the rubrickBlock as a markup string.
	 * 
	 * @param string $content
	 * @throws InvalidArgumentException If $content is not a string.
	 */
	public function setContent($content) {
		if (gettype($content) === 'string') {
			$this->content = $content;
		}
		else {
			$msg = "The content argument must be a string, '" . gettype($content) . "' given";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the content of the RubricBlock as a markup string.
	 * 
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}
	
	public function getQtiClassName() {
		return 'rubricBlock';
	}
	
	public function getComponents() {
		return new QtiComponentCollection(array_merge($this->getStylesheets()->getArrayCopy()));
	}
}
