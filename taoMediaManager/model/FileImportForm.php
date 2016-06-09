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
class FileImportForm extends \tao_helpers_form_FormContainer
{
    private $instanceUri;

    public function __construct($instanceUri)
    {
        $this->instanceUri = $instanceUri;
        parent::__construct();

    }

    protected function initForm()
    {
        $this->form = new \tao_helpers_form_xhtml_Form('export');
        $submitElt = \tao_helpers_form_FormFactory::getElement('import', 'Free');
        $submitElt->setValue('<a href="#" class="form-submitter btn-success small"><span class="icon-import"></span> ' . __('Import') . '</a>');

        $this->form->setActions(array($submitElt), 'bottom');
        $this->form->setActions(array(), 'top');

    }

    /**
     * Used to create the form elements and bind them to the form instance
     *
     * @access protected
     * @return mixed
     */
    protected function initElements()
    {
        //create file upload form box
        $fileElt = \tao_helpers_form_FormFactory::getElement('source', 'AsyncFile');
        $fileElt->setDescription(__("Add a media file"));
        if (isset($_POST['import_sent_file'])) {
            $fileElt->addValidator(\tao_helpers_form_FormFactory::getValidator('NotEmpty'));
        } else {
            $fileElt->addValidator(\tao_helpers_form_FormFactory::getValidator('NotEmpty', array('message' => '')));
        }
        $fileElt->addValidators(array(
            \tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => \tao_helpers_Environment::getFileUploadLimit()))
        ));

        $this->form->addElement($fileElt);

        $langService = \tao_models_classes_LanguageService::singleton();
        $dataUsage = new \core_kernel_classes_Resource(INSTANCE_LANGUAGE_USAGE_DATA);
        $dataLang = \common_session_SessionManager::getSession()->getDataLanguage();
        $dataLang = 'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.$dataLang;
        if(!is_null($this->instanceUri)){
            $instance = new \core_kernel_classes_Resource($this->instanceUri);
            $lang = $instance->getOnePropertyValue(new \core_kernel_classes_Property(MEDIA_LANGUAGE));
            if($lang instanceof \core_kernel_classes_Resource){
                $dataLang = $lang->getUri();
            }
        }
        
        $langOptions = array();
        foreach ($langService->getAvailableLanguagesByUsage($dataUsage) as $lang) {
            $langOptions[\tao_helpers_Uri::encode($lang->getUri())] = $lang->getLabel();
        }
        $langElt = \tao_helpers_form_FormFactory::getElement('lang', 'Combobox');
        $langElt->setValue(\tao_helpers_Uri::encode($dataLang));
        $langElt->setOptions($langOptions);
        $this->form->addElement($langElt);


        $this->form->createGroup('options', __('Media Options'), array(
            $langElt
        ));

        $fileSentElt = \tao_helpers_form_FormFactory::getElement('import_sent_file', 'Hidden');
        $fileSentElt->setValue(1);
        $this->form->addElement($fileSentElt);

        if (!is_null($this->instanceUri)) {
            $instanceElt = \tao_helpers_form_FormFactory::getElement('instanceUri', 'Hidden');
            $instanceElt->setValue($this->instanceUri);
            $this->form->addElement($instanceElt);
        }

    }
}
