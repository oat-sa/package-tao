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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */               
namespace oat\taoDeliveryRdf\helper;
/**
 * Helper to render the delivery form on the group page
 * 
 * @author joel bout, <joel@taotesting.com>
 * @package taoDelivery
 
 */
class DeliveryWidget
{
	public static function renderDeliveryTree(\core_kernel_classes_Resource $group) {

		// ensure constant is known since this helper can be called out of context
		\common_ext_ExtensionsManager::singleton()->getExtensionById('taoDeliveryRdf')->load();
		
		$property = new \core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY);
		$tree = \tao_helpers_form_GenerisTreeForm::buildTree($group, $property);
		$tree->setData('title', __('Deliveries'));
		return $tree->render();

	}
}