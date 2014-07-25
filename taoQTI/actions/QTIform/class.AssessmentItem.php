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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * Short description of class taoQTI_actions_QTIform_AssessmentItem
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10010
 * @subpackage actions_QTIform
 */
class taoQTI_actions_QTIform_AssessmentItem
    extends tao_helpers_form_FormContainer
{

    /**
     * Short description of attribute item
     *
     * @access protected
     * @var Item
     */
    protected $item = null;

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  Item item
     */
    public function __construct( taoQTI_models_classes_QTI_Item $item)
    {
		$this->item = $item;
		parent::__construct(array(), array());
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function initForm()
    {

		$this->form = tao_helpers_form_FormFactory::getForm('AssessmentItem_Form');

		$actions = array();

		$this->form->setActions($actions, 'top');
		$this->form->setActions(array(), 'bottom');

    }

    /**
     * Short description of method getItem
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return taoQTI_models_classes_QTI_Item
     */
    public function getItem()
    {
		$returnValue = $this->item;
        return $returnValue;
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function initElements()
    {

		//serial
		$serialElt = tao_helpers_form_FormFactory::getElement('itemSerial', 'Hidden');
		$serialElt->setValue($this->item->getSerial());
		$this->form->addElement($serialElt);

		//title:
		$titleElt = tao_helpers_form_FormFactory::getElement('title', 'Textbox');
		$titleElt->setDescription(__('Title'));
		$titleElt->setValue($this->item->getAttributeValue('title'));
		$this->form->addElement($titleElt);

		//label: not used, instead rather confusing for users
//		$labelElt = tao_helpers_form_FormFactory::getElement('label', 'Textbox');
//		$labelElt->setDescription(__('Label'));
//		$labelElt->setValue($this->item->getAttributeValue('label'));
//		$this->form->addElement($labelElt);
		
		//@TODO : funcitons not available yet, to be implemented
		$this->form->addElement(self::createBooleanElement($this->item, 'timeDependent', 'Time dependent'));
//		$this->form->addElement(self::createBooleanElement($this->item, 'adaptive', ''));

    }

    /**
     * Short description of method createBooleanElement
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  Data qtiObject
     * @param  string optionName
     * @param  string elementLabel
     * @param  array boolean
     * @return tao_helpers_form_FormElement
     */
    public static function createBooleanElement( taoQTI_models_classes_QTI_Element $qtiObject, $optionName, $elementLabel = '', $boolean = array('no', 'yes'))
    {

		if(count($boolean) != 2){
			throw new Exception('invalid number of elements in boolean array definition');
		}
		$returnValue = tao_helpers_form_FormFactory::getElement($optionName, 'Radiobox');

		if(empty($elementLabel)) {
		    $elementLabel = __(ucfirst(strtolower($optionName)));
		}
		$returnValue->setDescription($elementLabel);
		$returnValue->setOptions(array('true'=>$boolean[1], 'false' => $boolean[0]));

		$optionValue = $qtiObject->getAttributeValue($optionName);

		$returnValue->setValue('false');
		if(!empty($optionValue)){
			if($optionValue === 'true' || $optionValue === true){
				$returnValue->setValue('true');
			}
		}

        return $returnValue;
    }

    /**
     * Short description of method createTextboxElement
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  Data qtiObject
     * @param  string optionName
     * @param  string elementLabel
     * @return tao_helpers_form_FormElement
     */
    public static function createTextboxElement( taoQTI_models_classes_QTI_Element $qtiObject, $optionName, $elementLabel = '')
    {

		$returnValue = tao_helpers_form_FormFactory::getElement($optionName, 'Textbox');
		if(empty($elementLabel)) {
		    $elementLabel = __(ucfirst(strtolower($optionName)));
		}
		$returnValue->setDescription($elementLabel);

		//validator: is int??
		$value = (string) $qtiObject->getAttributeValue($optionName);
		if(!is_null($value)){
			$returnValue->setValue($value);
		}

        return $returnValue;
    }

}