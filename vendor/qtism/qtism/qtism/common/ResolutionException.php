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
 */
namespace qtism\common;

use \RuntimeException;
use \Exception;

/**
 * The ResolutionException must be thrown when an error occurs while
 * resolving something.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ResolutionException extends RuntimeException {
	
	/**
	 * Create a new ResolutionException.
	 * 
	 * @param string $message A human-readable description of the exception.
	 * @param Exception $previous An optional previous Exception that caused the exception to be thrown.
	 */
	public function __construct($message, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}