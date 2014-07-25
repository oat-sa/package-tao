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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoQTI/actions/QTIform/class.CSSuploader.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 03.07.2012, 14:40:43 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jehan Bihin, <jehan.bihin@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Jehan Bihin, <jehan.bihin@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAB-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAB-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAB-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAB-constants end

/**
 * Short description of class taoQTI_actions_QTIform_CSSuploader
 *
 * @access public
 * @author Jehan Bihin, <jehan.bihin@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */
class taoQTI_actions_QTIform_CSSuploader
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute item
     *
     * @access protected
     * @var Item
     */
    protected $item = null;

    /**
     * Short description of attribute itemUri
     *
     * @access public
     * @var string
     */
    public $itemUri = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  Item item
     * @param  string itemUri
     */
    public function __construct( taoQTI_models_classes_QTI_Item $item, $itemUri)
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAF begin

		$this->item = $item;
		$this->itemUri = $itemUri;
		$returnValue = parent::__construct(array(), array());

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FAF end
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     */
    public function initForm()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB3 begin

		$this->form = tao_helpers_form_FormFactory::getForm('css_uploader');

		$this->form->setDecorators(array(
			'element'			=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div')),
			'group'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-group')),
			'error'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-error ui-state-error ui-corner-all')),
			'help'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'span','cssClass' => 'form-help')),
			'actions-bottom'	=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar')),
			'actions-top'		=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar'))
		));

		$submitElt = tao_helpers_form_FormFactory::getElement('submit', 'Submit');
		$submitElt->setValue('Upload');
		$this->form->setActions(array($submitElt), 'bottom');

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB3 end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB5 begin

		$serialElt = tao_helpers_form_FormFactory::getElement('itemSerial', 'Hidden');
		$serialElt->setValue($this->item->getSerial());
		$this->form->addElement($serialElt);

		$elt = tao_helpers_form_FormFactory::getElement('itemUri', 'Hidden');
		$elt->setValue($this->itemUri);
		$this->form->addElement($elt);

		$labelElt = tao_helpers_form_FormFactory::getElement('title', 'Textbox');
		$labelElt->setDescription(__('File name'));
		$this->form->addElement($labelElt);

		$importFileElt = tao_helpers_form_FormFactory::getElement("css_import", 'AsyncFile');
		$importFileElt->setAttribute('auto', true);
		$importFileElt->setDescription(__("Upload the style sheet"));
		$importFileElt->setHelp(__("CSS format required"));
		$importFileElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('NotEmpty'),
			tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => 3000000)),
			tao_helpers_form_FormFactory::getValidator('FileMimeType', array('mimetype' => array('text/css', 'text/plain'), 'extension' => array('css')))
		));
		$this->form->addElement($importFileElt);

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000004FB5 end
    }

} /* end of class taoQTI_actions_QTIform_CSSuploader */

?>