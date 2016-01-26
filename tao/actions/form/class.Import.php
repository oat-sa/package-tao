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

/**
 * This container initialize the import form.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_actions_form_Import extends tao_helpers_form_FormContainer
{
	// --- ASSOCIATIONS ---


	// --- ATTRIBUTES ---

	private $importHandlers = array();

	/**
	 * @var tao_helpers_form_Form
	 */
	private $subForm = null;
	// --- OPERATIONS ---

	/**
	 * Initialise the form for the given importHandlers
	 *
	 * @param tao_models_classes_import_ImportHandler $importHandler
	 * @param array $availableHandlers
	 * @param core_kernel_classes_Resource $class
	 * @internal param array $importHandlers
	 * @internal param tao_helpers_form_Form $subForm
	 */
	public function __construct($importHandler, $availableHandlers, $class)
	{
		$this->importHandlers = $availableHandlers;
		if (!is_null($importHandler)) {
		    $this->subForm = $importHandler->getForm();
		}
		parent::__construct(array(
			'importHandler' => get_class($importHandler),
			'classUri'		=> $class->getUri(),
		    'id'            => $class->getUri()
		));
	}

	/**
	 * inits the import form
	 *
	 * @access public
	 * @author Joel Bout, <joel.bout@tudor.lu>
	 * @return mixed
	 */
	public function initForm()
	{
		$this->form = tao_helpers_form_FormFactory::getForm('import');
	    
		$this->form->setActions(is_null($this->subForm) ? array() : $this->subForm->getActions('top'), 'top');
	    $this->form->setActions(is_null($this->subForm) ? array() : $this->subForm->getActions('bottom'), 'bottom');
				 
	}

	/**
	 * Inits the element to select the importhandler
	 * and takes over the elements of the import form
	 *
	 * @access public
	 * @author Joel Bout, <joel.bout@tudor.lu>
	 * @return mixed
	 */
	public function initElements()
	{
		//create the element to select the import format
		$formatElt = tao_helpers_form_FormFactory::getElement('importHandler', 'Radiobox');
		$formatElt->setDescription(__('Choose import format'));
		$formatElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty')); // should never happen anyway
		$importHandlerOptions= array();
		foreach ($this->importHandlers as $importHandler) {
			$importHandlerOptions[get_class($importHandler)] = $importHandler->getLabel();
		}
		$formatElt->setOptions($importHandlerOptions);
		

		$classUriElt = tao_helpers_form_FormFactory::getElement('classUri', 'Hidden');
//		$classUriElt->setValue($class->getUri());
		$this->form->addElement($classUriElt);

		$classUriElt = tao_helpers_form_FormFactory::getElement('id', 'Hidden');
		$this->form->addElement($classUriElt);
		
		$this->form->addElement($formatElt);

		if (!is_null($this->subForm)) {
//			load dynamically the method regarding the selected format
			$this->form->setElements(array_merge($this->form->getElements(), $this->subForm->getElements()));
			foreach ($this->subForm->getGroups() as $key => $group) {
				$this->form->createGroup($key,$group['title'],$group['elements'],$group['options']);
			}
		}
	}

}
