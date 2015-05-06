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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\tao\helpers\Template;

/**
 * Export form for QTI packages
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 
 */
class tao_actions_form_NamespaceExportForm
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---
    /**
     * Short description of method initForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        $this->form = new tao_helpers_form_xhtml_Form('export');
		$this->form->setDecorators(array(
			'element'			=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div')),
			'error'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-error ui-state-error ui-corner-all')),
			'actions-bottom'	=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar')),
			'actions-top'		=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar'))
		));

    	$exportElt = tao_helpers_form_FormFactory::getElement('export', 'Free');
		$exportElt->setValue( "<a href='#' class='form-submiter' ><img src='".TAOBASE_WWW."/img/export.png' /> ".__('Export')."</a>");

		$this->form->setActions(array($exportElt), 'bottom');
    }
    
    /**
     * overriden
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {

		$nameElt = tao_helpers_form_FormFactory::getElement('filename', 'Textbox');
		$nameElt->setDescription(__('File name'));
		$nameElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$nameElt->setUnit(".rdf");
		$this->form->addElement($nameElt);

		$nsManager = common_ext_NamespaceManager::singleton();

		$tplElt = new tao_helpers_form_elements_template_Template('rdftpl');
		$tplElt->setPath(Template::getTemplate('settings/namespaceExport.tpl', 'tao'));
		$tplElt->setVariables(array(
			'namespaces' 	=> $nsManager->getAllNamespaces(),
			'localNs'		=> $nsManager->getLocalNamespace()->getModelId()
		));
		$this->form->addElement($tplElt);


    }

}