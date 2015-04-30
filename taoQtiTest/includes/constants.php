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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
$todefine = array(
    // Ontology references.
    'INSTANCE_QTITEST_TESTRUNNERSERVICE' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ServiceQtiTestRunner',
    'INSTANCE_QTITEST_ITEMRUNNERSERVICE' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ServiceQtiTestItemRunner',
    'INSTANCE_FORMALPARAM_QTITEST_TESTDEFINITION' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#FormalParamQtiTestDefinition',
    'INSTANCE_FORMALPARAM_QTITEST_TESTCOMPILATION' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#FormalParamQtiTestCompilation',
    'INSTANCE_FORMALPARAM_QTITESTITEMRUNNER_PARENTCALLID' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#FormalParamQtiTestParentServiceCallId',
    'INSTANCE_TEST_MODEL_QTI' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#QtiTestModel',
                
    // Configuration.
   'TAOQTITEST_FILENAME' => 'tao-qtitest-testdefinition.xml',
   'TAOQTITEST_COMPILED_FILENAME' => 'compact-test.php',
   'TAOQTITEST_COMPILED_META_FILENAME' => 'test-meta.php',
   'TAOQTITEST_REMOTE_FOLDER' => 'tao-qtitest-remote',
   'TAOQTITEST_RENDERING_STATE_NAME' => 'taoQtiTestState',
   'TAOQTITEST_BASE_PATH_NAME' => 'taoQtiBasePath',
   'TAOQTITEST_PLACEHOLDER_BASE_URI' => 'tao://qti-directory',
   'TAOQTITEST_VIEWS_NAME' => 'taoQtiViews'
);