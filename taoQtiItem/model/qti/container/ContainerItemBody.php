<?php
/*  
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
 * 
 */

namespace oat\taoQtiItem\model\qti\container;

use oat\taoQtiItem\model\qti\container\ContainerItemBody;
use oat\taoQtiItem\model\qti\container\ContainerInteractive;

/**
 * The QTI ContainerItemBody reprensents the ItemBody or an assessmentItem
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class ContainerItemBody extends ContainerInteractive
{
	
	/**
     * return the list of available element classes
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return array
     */
	public function getValidElementTypes(){
		return array(
			'oat\\taoQtiItem\\model\\qti\\Img',
			'oat\\taoQtiItem\\model\\qti\\Math',
			'oat\\taoQtiItem\\model\\qti\\feedback\\Feedback',
			'taoQTI_models_classes_QTI_PrintedVariable',
			'oat\\taoQtiItem\\model\\qti\\Object',
			'oat\\taoQtiItem\\model\\qti\\interaction\\Interaction',
			'oat\\taoQtiItem\\model\\qti\\RubricBlock',
			'oat\\taoQtiItem\\model\\qti\\InfoControl'
		);
	}
	
}