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
namespace qtism\runtime\common;

use qtism\data\QtiComponent;

/**
 * The ProcessorFactory must be implemented by any factory class that produces
 * Processable objects.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
interface ProcessorFactory {
	
	/**
	 * Create a Processable object able to process $component.
	 * 
	 * @param QtiComponent $component A QtiComponent object that the returned Processable object is able to process.
	 * @return Processable A Processable object able to process $component.
	 */
	public function createProcessor(QtiComponent $component);
}