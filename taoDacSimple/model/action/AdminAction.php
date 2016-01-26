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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\taoDacSimple\model\action;

use oat\tao\model\menu\Icon;
use oat\taoBackOffice\model\menuStructure\Action;
/**
 * Admin Action, based on xml:
 * 
 * <action id="access-control-admin" name="Access control" url="/taoDacSimple/AdminAccessController/adminPermissions" group="tree" context="resource">
 *     <icon id="icon-unlock" />
 * </action>
 */
class AdminAction implements Action {
    
    /**
     * @return string Identifier of action
     */
    public function getId()
    {
        return 'access-control-admin';
    }
    
    /**
     * @return string Label of the action
     */
    public function getName()
    {
        return __('Access control');
    }

    /**
     * @return string fully qualified URL of the action
     */
    public function getUrl()
    {
        return _url('adminPermissions', 'AdminAccessController', 'taoDacSimple');
    }
    
    /**
     * @return string Java script bindings of action
     */
    public function getBinding()
    {
        return self::BINDING_DEFAULT;
    }
    
    /**
     * @return string Context of the action
     */
    public function getContext()
    {
        return self::CONTEXT_RESOURCE;
    }

    /**
     * @return string Group of the action (where to display)
     */
    public function getGroup()
    {
        return self::GROUP_DEFAULT;
    }
    
    /**
     * @return Icon Icon to be used with action
     */
    public function getIcon()
    {
        return new Icon(array(
            'id' =>  'icon-unlock',
            'ext' => 'taoDacSimple',
            'src' => null
        ));
    }

}
