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

use qtism\common\ResolutionException;

/**
 * The LocalFileResolver class resolve relative paths to canonical
 * ones using a given base path.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class LocalFileResolver extends FileResolver {
	
	/**
	 * Create a new LocalFileResolver object.
	 * 
	 * @param string $basePath The base path from were the URLs will be resolved.
	 * @throws InvalidArgumentException If $basePath is not a valid string value.
	 */
	public function __construct($basePath = '') {
		parent::__construct($basePath);
	}
	
	/**
	 * Resolve a relative $url to a canonical path based
	 * on the LocalFileResolver's base path.
	 * 
	 * @param string $url A URL to be resolved.
	 * @throws ResolutionException If $url cannot be resolved.
	 */
	public function resolve($url) {
		$baseUrl = Utils::sanitizeUri($this->getBasePath());
		$baseDir = pathinfo($baseUrl, PATHINFO_DIRNAME);
			
		if (empty($baseDir)) {
			$msg = "The base directory of the document ('${baseDir}') could not be resolved.";
			throw new ResolutionException($msg);
		}
			
		$href = $baseDir . '/' . ltrim($url, '/');
		return $href;
	}
}
