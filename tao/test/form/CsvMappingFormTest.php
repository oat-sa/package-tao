<?php

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\tao\model\passwordRecovery\PasswordRecoveryService;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class CsvMappingFormTestCase extends TaoPhpUnitTestRunner
{
    public function testMapping()
    {

        $csv_column = array('login', 'password', 'title', 'last-name', 'firstname', 'gender', 'mail', 'token', 'abelabel');

        $properties = tao_helpers_Uri::encodeArray(array(
            RDFS_LABEL => 'Label',
            PROPERTY_USER_FIRSTNAME => 'First Name',
            PROPERTY_USER_LASTNAME => 'Last Name',
            PROPERTY_USER_LOGIN => 'Login',
            PROPERTY_USER_PASSWORD => 'Password',
            PROPERTY_USER_MAIL => 'Mail',
            PROPERTY_USER_UILG => 'Interface Language',
            PasswordRecoveryService::PROPERTY_PASSWORD_RECOVERY_TOKEN => 'Password recovery token',
        ), tao_helpers_Uri::ENCODE_ARRAY_KEYS);
        
        $data = array();
        $options = array(
            'class_properties' => $properties,
            'ranged_properties' => array(),
            'csv_column' => $csv_column,
            tao_helpers_data_CsvFile::FIRST_ROW_COLUMN_NAMES => true,
        );

        $formContainer = new tao_models_classes_import_CSVMappingForm($data, $options);
        $form = $formContainer->getForm();

        $this->assertEquals('csv_select', $form->getElement('http_2_www_0_w3_0_org_1_2000_1_01_1_rdf-schema_3_label')->getEvaluatedValue());
        $this->assertEquals('4_O', $form->getElement('http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_userFirstName')->getEvaluatedValue());
        $this->assertEquals('3_O', $form->getElement('http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_userLastName')->getEvaluatedValue());
        $this->assertEquals('0_O', $form->getElement('http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_login')->getEvaluatedValue());
        $this->assertEquals('1_O', $form->getElement('http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_password')->getEvaluatedValue());
        $this->assertEquals('6_O', $form->getElement('http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_userMail')->getEvaluatedValue());
        $this->assertEquals('csv_select', $form->getElement('http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_userUILg')->getEvaluatedValue());
        $this->assertEquals('csv_select', $form->getElement('http_2_www_0_tao_0_lu_1_Ontologies_1_generis_0_rdf_3_passwordRecoveryToken')->getEvaluatedValue());

    }
}
