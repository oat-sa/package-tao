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


namespace qtism\data\storage;

use qtism\common\Resolver;
use \InvalidArgumentException;

/**
 * The base class of FileResolvers.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class FileResolver implements Resolver {
    
    /**
     * A base path.
     * 
     * @var string
     */
    private $basePath = '';
    
    /**
     * Create a new FileResolver object.
     * 
     * @param string $basePath A base path.
     * @throws InvalidArgumentException If $basePath is not a string value.
     */
    public function __construct($basePath = '') {
        $this->setBasePath($basePath);
    }
    
    /**
     * Set the basePath.
     * 
     * @param string $basePath A base path.
     * @throws InvalidArgumentException If $basePath is not a string value.
     */
    public function setBasePath($basePath = '') {
		if (gettype($basePath) === 'string') {
			$this->basePath = $basePath;
		}
		else {
			$msg = "The basePath argument must be a valid string, '" . gettype($basePath) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
    
	/**
	 * Get the base path.
	 * 
	 * @return string A base path.
	 */
    public function getBasePath() {
		return $this->basePath;
	}
}
