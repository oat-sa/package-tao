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

namespace oat\taoMediaManager\model;

/**
 * Service methods to manage the Media
 *
 * @access public
 * @package taoMediaManager
 */
class editInstanceForm extends \tao_actions_form_Instance
{

    protected function initForm()
    {
        parent::initForm();
        $bottom = $this->form->getActions('bottom');
        $top = $this->form->getActions('top');

        $edit = \tao_helpers_form_FormFactory::getElement('edit', 'Free');
        $value = '';
        if($edit){
            $value .=  '<button type="button" id="edit-media" data-classuri="' . $this->getClazz()->getUri() . '" data-uri="' . $this->getInstance()->getUri() . '" class="edit-instance btn-success small"><span class="icon-upload"></span> ' . __('Upload new media') . '</button>';
        }

        $edit->setValue($value);
        $top[] = $edit;
        $bottom[] = $edit;

        $this->form->setActions($bottom, 'bottom');
        $this->form->setActions($top, 'top');

    }
}
