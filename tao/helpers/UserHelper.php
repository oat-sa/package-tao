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

namespace oat\tao\helpers;

use core_kernel_classes_Resource;
/**
 * Utility class to render a User
 *
 * @author Joel Bout <joel@taotesting.com>
 * @package tao
 */
class UserHelper
{
    static public function renderHtmlUser($userId)
    {
        // assume generis user
        $user = new core_kernel_classes_Resource($userId);
        $props = $user->getPropertiesValues(array(
        	RDFS_LABEL,
            PROPERTY_USER_MAIL
        ));
        $label = (isset($props[RDFS_LABEL]) && !empty($props[RDFS_LABEL])) ? (string)reset($props[RDFS_LABEL]) : '('.$userId.')'; 
        $mail = (isset($props[PROPERTY_USER_MAIL]) && !empty($props[PROPERTY_USER_MAIL])) ? (string)reset($props[PROPERTY_USER_MAIL]) : '';
        return !empty($mail)
            ? '<a href="mailto:'.$mail.'">'.$label.'</a>'
            : $label;
    }
}