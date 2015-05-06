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
namespace qtism\runtime\expressions;

use qtism\runtime\common\Processable;
use qtism\runtime\common\ProcessingException;
use \InvalidArgumentException;

/**
 * An Exception to be thrown in an Expression Processing context.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ExpressionProcessingException extends ProcessingException {
	
	/**
	 * Set the source of the error.
	 * 
	 * @param Processable $source The source of the error.
	 * @throws InvalidArgumentException If $source is not an ExpressionProcessor object.
	 */
	public function setSource(Processable $source) {
		if ($source instanceof ExpressionProcessor) {
			parent::setSource($source);
		}
		else {
			$msg = "ExpressionProcessingException::setSource only accept ExpressionProcessor objects.";
			throw new InvalidArgumentException($msg);
		}
	}
}