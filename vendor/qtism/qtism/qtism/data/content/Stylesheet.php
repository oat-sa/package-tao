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

use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \InvalidArgumentException;

/**
 * The Stylesheet class used to associate an external stylesheet with
 * an AssessmentItem.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Stylesheet extends QtiComponent {
	
	/**
	 * From IMS QTI:
	 * 
	 * The identifier or location of the external stylesheet.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $href;
	
	/**
	 * From IMS QTI:
	 * 
	 * The type of the external stylesheet.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $type = 'text/css';
	
	/**
	 * From IMS QTI:
	 * 
	 * An optional media descriptor that describes the media to which this 
	 * stylesheet applies.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $media = 'screen';
	
	/**
	 * From IMS QTI:
	 * 
	 * An optional title for the stylesheet.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $title = '';
	
	/**
	 * Create a new instance of Stylesheet.
	 * 
	 * @param string $href The hypertext reference (as a URI) to the stylesheet file.
	 * @param string $type The mime-type of the stylesheet. Default is 'text/css'.
	 * @param string $media The media to apply the stylesheet on. Default is 'screen'.
	 * @param string $title The title of the stylesheet.
	 */
	public function __construct($href, $type = 'text/css', $media = 'screen', $title = '') {
		$this->setHref($href);
		$this->setType($type);
		$this->setMedia($media);
		$this->setTitle($title);
	}
	
	/**
	 * Get the hypertext reference to the stylesheet.
	 * 
	 * @return string
	 */
	public function getHref() {
		return $this->href;
	}
	
	/**
	 * Set the hypertext reference to the stylesheet.
	 * 
	 * @param string $href An hypertext reference (as a URI).
	 * @throws InvalidArgumentException If $href is not a string.
	 */
	public function setHref($href) {
		if (gettype($href) === 'string') {
			$this->href = $href;
		}
		else {
			$msg = "Href must be a string, '" . gettype($href) . "' given.";
			throw new InvalidArgumentException($href);
		}
	}
	
	/**
	 * Get the mime-type of the stylesheet. Default value is 'text/css'.
	 * 
	 * @return string A mime-type.
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Set the mime-type of the stylesheet.
	 * 
	 * @param string $type A mime-type.
	 * @throws InvalidArgumentException If $type is not a string.
	 */
	public function setType($type) {
		if (gettype($type) === 'string') {
			$this->type = $type;
		}
		else {
			$msg = "Type must be a string, '" . gettype($href) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the media to apply the stylesheet on. Default is 'media'.
	 * 
	 * @return string A media.
	 */
	public function getMedia() {
		return $this->media;
	}
	
	/**
	 * Set the media to apply the stylesheet on.
	 * 
	 * @param string $media A media.
	 * @throws InvalidArgumentException If $media is not a string.
	 */
	public function setMedia($media) {
		if (is_string($media)) {
			$this->media = $media;
		}
		else {
			$msg = "Media must be a string, '" . gettype($media) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Whether a value is defined for the media attribute.
	 * 
	 * @return boolean
	 */
	public function hasMedia() {
	    return $this->getMedia() !== '';
	}
	
	/**
	 * Get the title of the stylesheet. Returns an empty string if not specified.
	 * 
	 * @return string A title or an empty string if not specified.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Set the title of the stylesheet.
	 * 
	 * @param string $title A title.
	 * @throws InvalidArgumentException If $title is not a string.
	 */
	public function setTitle($title) {
		if (gettype($title) === 'string') {
			$this->title = $title;
		}
		else {
			$msg = "Title must be a string, '" . gettype($title) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Whether a value is defined for the title attribute.
	 * 
	 * @return boolean
	 */
	public function hasTitle() {
	    return $this->getTitle() !== '';
	}
	
	public function getQtiClassName() {
		return 'stylesheet';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
}
