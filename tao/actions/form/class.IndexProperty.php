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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Enable you to edit a property
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_actions_form_IndexProperty
    extends tao_actions_form_AbstractProperty
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---


    /**
     * Initialize the form elements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initElements()
    {

    	$elementNames = array();

        //index part
        $indexProperty = null;
        $indexUri = '';
        if(!is_null($this->instance)){
            $indexUri = $this->instance->getUri();
            $indexProperty = new \oat\tao\model\search\Index($indexUri);
            $indexUri = tao_helpers_Uri::encode($indexUri);
        }
        (isset($this->options['property'])) ? $propertyUri = $this->options['property'] : $propertyUri = 1;
        (isset($this->options['index'])) ? $index = $this->options['index'] : $index = 1;
        (isset($this->options['propertyindex'])) ? $propertyindex = $this->options['propertyindex'] : $propertyindex = 1;



        //get and add Label (Text)
        $label = (!is_null($indexProperty))?$indexProperty->getLabel():'';
        $propIndexElt = tao_helpers_form_FormFactory::getElement("index_{$propertyindex}{$index}_".tao_helpers_Uri::encode(RDFS_LABEL), 'Textbox');
        $propIndexElt->setDescription(__('Label'));
        $propIndexElt->addAttribute('class', 'index');
        $propIndexElt->addAttribute('data-related-index', $indexUri);
        $propIndexElt->setValue($label);
        $this->form->addElement($propIndexElt);
        $elementNames[] = $propIndexElt->getName();


        //get and add Fuzzy matching (Radiobox)
        $fuzzyMatching = (!is_null($indexProperty))?($indexProperty->isFuzzyMatching())?GENERIS_TRUE:GENERIS_FALSE:'';
        $options = array(
            tao_helpers_Uri::encode(GENERIS_TRUE)  => __('True'),
            tao_helpers_Uri::encode(GENERIS_FALSE) => __('False')
        );
        $propIndexElt = tao_helpers_form_FormFactory::getElement("{$propertyindex}{$index}_".tao_helpers_Uri::encode(INDEX_PROPERTY_FUZZY_MATCHING), 'Radiobox');
        $propIndexElt->setOptions($options);
        $propIndexElt->setDescription(__('Fuzzy Matching'));
        $propIndexElt->addAttribute('class', 'index');
        $propIndexElt->addAttribute('data-related-index', $indexUri);
        $propIndexElt->setValue(tao_helpers_Uri::encode($fuzzyMatching));
        $propIndexElt->addValidator(new tao_helpers_form_validators_NotEmpty());
        $this->form->addElement($propIndexElt);
        $elementNames[] = $propIndexElt->getName();

        //get and add identifier (Text)
        $identifier = (!is_null($indexProperty))?$indexProperty->getIdentifier():'';
        $propIndexElt = tao_helpers_form_FormFactory::getElement("{$propertyindex}{$index}_".tao_helpers_Uri::encode(INDEX_PROPERTY_IDENTIFIER), 'Textbox');
        $propIndexElt->setDescription(__('Identifier'));
        $propIndexElt->addAttribute('class', 'index');
        $propIndexElt->addAttribute('data-related-index', $indexUri);
        $propIndexElt->setValue($identifier);
        $propIndexElt->addValidator(new tao_helpers_form_validators_IndexIdentifier());
        $this->form->addElement($propIndexElt);
        $elementNames[] = $propIndexElt->getName();


        //get and add Default search
        $defaultSearch = (!is_null($indexProperty))?($indexProperty->isDefaultSearchable())?GENERIS_TRUE:GENERIS_FALSE:'';
        $options = array(
            tao_helpers_Uri::encode(GENERIS_TRUE)  => __('True'),
            tao_helpers_Uri::encode(GENERIS_FALSE) => __('False')
        );
        $propIndexElt = tao_helpers_form_FormFactory::getElement("{$propertyindex}{$index}_".tao_helpers_Uri::encode(INDEX_PROPERTY_DEFAULT_SEARCH), 'Radiobox');
        $propIndexElt->setOptions($options);
        $propIndexElt->setDescription(__('Default search'));
        $propIndexElt->addAttribute('class', 'index');
        $propIndexElt->addAttribute('data-related-index', $indexUri);
        $propIndexElt->setValue(tao_helpers_Uri::encode($defaultSearch));
        $propIndexElt->addValidator(new tao_helpers_form_validators_NotEmpty());
        $this->form->addElement($propIndexElt);
        $elementNames[] = $propIndexElt->getName();

        //get and add Tokenizer (Combobox)
        $tokenizerRange = new \core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#Tokenizer');
        $options = array();
        /** @var core_kernel_classes_Resource $value */
        foreach($tokenizerRange->getInstances() as $value){
            $options[tao_helpers_Uri::encode($value->getUri())] = $value->getLabel();
        }
        $tokenizer = (!is_null($indexProperty))?$indexProperty->getOnePropertyValue(new \core_kernel_classes_Property(INDEX_PROPERTY_TOKENIZER)):null;
        $tokenizer = (get_class($tokenizer) === 'core_kernel_classes_Resource')?$tokenizer->getUri():'';
        $propIndexElt = tao_helpers_form_FormFactory::getElement("{$propertyindex}{$index}_".tao_helpers_Uri::encode(INDEX_PROPERTY_TOKENIZER), 'Combobox');
        $propIndexElt->setDescription(__('Tokenizer'));
        $propIndexElt->addAttribute('class', 'index');
        $propIndexElt->addAttribute('data-related-index', $indexUri);
        $propIndexElt->setOptions($options);
        $propIndexElt->setValue(tao_helpers_Uri::encode($tokenizer));
        $propIndexElt->addValidator(new tao_helpers_form_validators_NotEmpty());
        $this->form->addElement($propIndexElt);
        $elementNames[] = $propIndexElt->getName();

        $removeIndexElt = tao_helpers_form_FormFactory::getElement("index_{$indexUri}_remove", 'Free');
        $removeIndexElt->setValue(
            "<a href='#' id='{$indexUri}' class='btn-error index-remover small' data-index='{$index}'><span class='icon-remove'></span> " . __(
                'remove index'
            ) . "</a>"
        );
        $this->form->addElement($removeIndexElt);
        $elementNames[] = $removeIndexElt;

        $separatorIndexElt = tao_helpers_form_FormFactory::getElement("index_{$propertyindex}{$indexUri}_separator", 'Free');
        $separatorIndexElt->setValue(
            "<hr class='index' data-related-index='{$indexUri}'>"
        );
        $this->form->addElement($separatorIndexElt);
        $elementNames[] = $separatorIndexElt;

    }

}