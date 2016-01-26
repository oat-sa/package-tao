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
namespace oat\taoBackOffice\model\menuStructure;

use oat\tao\model\menu\Icon;
/**
 * Action interface
 */
interface Action {
    
    /**
     * context of actions that apply to all resources
     * @var string
     */
    const CONTEXT_RESOURCE = 'resource';
    
    /**
     * context of actions that only apply to classes 
     * @var string
     */
    const CONTEXT_CLASS = 'class';
    
    /**
     * context of actions that only apply to instances
     * @var string
     */
    const CONTEXT_INSTANCE = 'instance';
    
    /**
     * Default js binding of actions
     * @var string
     */
    const BINDING_DEFAULT = 'load';
    
    /**
     * Default group to add the action to
     * @var string
     */
    const GROUP_DEFAULT = 'tree';
    
    /**
     * @return string Identifier of action
     */
    public function getId();
    
    /**
     * @return string Label of the action
     */
    public function getName();

    /**
     * @return string fully qualified URL of the action
     */
    public function getUrl();
    
    /**
     * @return string Java script bindings of action
     */
    public function getBinding();
    
    /**
     * @return string Context of the action
     */
    public function getContext();

    /**
     * @return string Group of the action (where to display)
     */
    public function getGroup();
    
    /**
     * @return Icon Icon to be used with action
     */
    public function getIcon();

}