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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2014 (update and modification) Open Assessment Technologies SA
 */
namespace oat\taoGroups\controller;

use \core_kernel_classes_Property;
use \tao_helpers_form_GenerisTreeForm;
use oat\taoGroups\models\GroupsService;

/**
 * SaSGroups Controller provide process services
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 *         
 * @license GPLv2 http://www.opensource.org/licenses/gpl-2.0.php
 */
class SaSGroups extends Groups
{

    protected function getClassService()
    {
        return GroupsService::singleton();
    }

    /**
     * overrided to prevent exception:
     * if no class is selected, the root class is returned
     * 
     * @see TaoModule::getCurrentClass()
     * @return core_kernel_class_Class
     */
    protected function getCurrentClass()
    {
        if ($this->hasRequestParameter('classUri')) {
            return parent::getCurrentClass();
        }
        return $this->getRootClass();
    }

    /**
     * Render the tree to select the group related subjects
     * 
     */
    public function selectSubjects()
    {
        $memberProperty = new core_kernel_classes_Property(GroupsService::PROPERTY_MEMBERS_URI);
        $memberForm = tao_helpers_form_GenerisTreeForm::buildTree($this->getCurrentInstance(), $memberProperty);
        $memberForm->setData('title', __('Select group test takers'));
        $this->setData('tree', $memberForm->render());
        $this->setView('sas' . DIRECTORY_SEPARATOR . 'generisTreeSelect.tpl', 'tao');
    }
}