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
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package taoMediaManager
 */
class ZipExportForm extends \tao_helpers_form_FormContainer
{

    public function initForm()
    {


        $this->form = new \tao_helpers_form_xhtml_Form('export');

        $this->form->setDecorators(array(
                'element'			=> new \tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div')),
                'group'				=> new \tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-group')),
                'error'				=> new \tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-error ui-state-error ui-corner-all')),
                'actions-bottom'	=> new \tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar')),
                //'actions-top'		=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar'))
            ));

        $exportElt = \tao_helpers_form_FormFactory::getElement('export', 'Free');
        $exportElt->setValue('<a href="#" class="form-submitter btn-success small"><span class="icon-export"></span> ' . __('Export') . '</a>');

        $this->form->setActions(array($exportElt), 'bottom');

    }


    public function initElements()
    {
        if (isset($this->data['resource'])) {
            $resource = $this->data['resource'];
        } else {
            throw new \common_Exception('No class nor instance specified for export');
        }

        $fileName = strtolower(\tao_helpers_Display::textCleaner($resource->getLabel(), '*'));

        $hiddenElt = \tao_helpers_form_FormFactory::getElement('resource', 'Hidden');
        $hiddenElt->setValue($resource->getUri());
        $this->form->addElement($hiddenElt);


        $nameElt = \tao_helpers_form_FormFactory::getElement('filename', 'Textbox');
        $nameElt->setDescription(__('File name'));
        $nameElt->addValidator(\tao_helpers_form_FormFactory::getValidator('NotEmpty'));
        $nameElt->setValue($fileName);
        $nameElt->setUnit(".zip");
        $this->form->addElement($nameElt);

        $this->form->createGroup('options', __('Export Media as Zip file'), array('filename', 'ziptpl'));
    }
}
