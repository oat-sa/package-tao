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
 * 
 */

/**
 * Helper to render the groups form on the user pane
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package core
 * @subpackage kernel_auth_adapter
 */
class taoGroups_helpers_SubjectForm
{
    /**
     * Returns a form to modify the groups a user is part of 
     * 
     * @param core_kernel_classes_Resource $subject
     * @return string
     */
    public static function renderGroupTreeForm(core_kernel_classes_Resource $subject) {
    	
        // groups constants loaded when checking if extention is installed
        $memberProperty = new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP);
		$groupForm = tao_helpers_form_GenerisTreeForm::buildReverseTree($subject, $memberProperty);
		$groupForm->setData('title',	__('Add to group '));
		return $groupForm->render();
    }
}