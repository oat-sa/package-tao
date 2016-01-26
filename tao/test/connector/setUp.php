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
?>
<?php
include_once dirname(__FILE__) . '/../../includes/raw_start.php';

$testUserData = array(
	PROPERTY_USER_LOGIN		=> 	'tjdoe',
	PROPERTY_USER_PASSWORD	=>	'test123',
	PROPERTY_USER_LASTNAME	=>	'Doe',
	PROPERTY_USER_FIRSTNAME	=>	'John',
	PROPERTY_USER_MAIL		=>	'jdoe@tao.lu',
	PROPERTY_USER_DEFLG		=>	tao_models_classes_LanguageService::singleton()->getLanguageByCode(DEFAULT_LANG)->getUri(),
	PROPERTY_USER_UILG		=>	tao_models_classes_LanguageService::singleton()->getLanguageByCode(DEFAULT_LANG)->getUri(),
	PROPERTY_USER_ROLES		=>  array(INSTANCE_ROLE_GLOBALMANAGER)
);

$testUserData[PROPERTY_USER_PASSWORD] = 'test'.rand();
		
$data = $testUserData;
$data[PROPERTY_USER_PASSWORD] = core_kernel_users_Service::getPasswordHash()->encrypt($data[PROPERTY_USER_PASSWORD]);
$tmclass = new core_kernel_classes_Class(CLASS_TAO_USER);
$user = $tmclass->createInstanceWithProperties($data);
common_Logger::i('Created user '.$user->getUri());

// prepare a lookup table of languages and values
$usage = new core_kernel_classes_Resource(INSTANCE_LANGUAGE_USAGE_GUI);
$propValue = new core_kernel_classes_Property(RDF_VALUE);
$langService = tao_models_classes_LanguageService::singleton();

$lookup = array();
foreach ($langService->getAvailableLanguagesByUsage($usage) as $lang) {
	$lookup[$lang->getUri()] = (string)$lang->getUniquePropertyValue($propValue);
}

echo json_encode(array(
	'rootUrl'	=> ROOT_URL,
	'userUri'	=> $user->getUri(),
	'userData'	=> $testUserData,
	'lang'		=> $lookup	
));