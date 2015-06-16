<?php

use oat\taoGroups\models\GroupsService;
use oat\taoDevTools\helper\NameGenerator;
use oat\authKeyValue\helpers\DataGeneration;
use oat\authKeyValue\AuthKeyValueUserService;
$parms = $argv;
array_shift($parms);

if (count($parms) < 2 || count($parms) > 3) {
    echo 'Usage: '.__FILE__.' TAOROOT CSVFILE [GROUPURI]'.PHP_EOL;
    die(1);
}

$root = rtrim(array_shift($parms), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
$csvfile = array_shift($parms);
$groupUri = empty($parms) ? null : array_shift($parms);

$rawStart = $root.'tao'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'raw_start.php';

if (!file_exists($rawStart)) {
    echo 'Tao not found at "'.$rawStart.'"'.PHP_EOL;
    die(1);
}
require_once $rawStart;

if (!file_exists($csvfile)) {
    echo 'Csv file not found at "'.$csvfile.'"'.PHP_EOL;
    die(1);
}

if (is_null($groupUri)) {
    $label = 'Group '.NameGenerator::generateRandomString(4);
    $groupClass = new \core_kernel_classes_Class(TAO_GROUP_CLASS);
    $group = $groupClass->createInstanceWithProperties(array(
        RDFS_LABEL => $label
    ));
    echo 'Group "'.$label.'" created.'.PHP_EOL;
    $groupUri = $group->getUri();
} else {
    $group = new core_kernel_classes_Resource($groupUri);
    if (!$group->exists()) {
        echo 'Group "'.$groupUri.'" not found.'.PHP_EOL;
        die(1);
    }
}

$expected = array(
    'label' => RDFS_LABEL,
    'login' => PROPERTY_USER_LOGIN,
    'password' => PROPERTY_USER_PASSWORD,
    'lastname' => PROPERTY_USER_LASTNAME,
    'firstname' => PROPERTY_USER_FIRSTNAME
);
$keys = array_keys($expected);
$userService = new AuthKeyValueUserService();
$persistence = \common_persistence_Manager::getPersistence('default')

$row = 1;
if (($handle = fopen($csvfile, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($row === 1) {
            if (json_encode($data) != json_encode(array_keys($expected))) {
                echo 'Expected data in the format "'.implode('","', $expected).'".'.PHP_EOL;
                die(1);
            }
        } else {
            $toAdd = array(
            	GroupsService::PROPERTY_MEMBERS_URI => array($groupUri)
            );
            foreach ($data as $pos => $value) {
                $toAdd[$expected[$keys[$pos]]] = $value;
            }
            
            // encode password
            $toAdd[PROPERTY_USER_PASSWORD] = core_kernel_users_Service::getPasswordHash()->encrypt($toAdd[PROPERTY_USER_PASSWORD]);

            if ($userService->getUserData($toAdd[PROPERTY_USER_LOGIN]) != false) {
                echo 'User "'.$toAdd[PROPERTY_USER_LOGIN].'" already exists.'.PHP_EOL;
                die(1); 
            }
            $userData = DataGeneration::createUser($toAdd);
            $persistence->insert(
                'redis',
                array(
                    'subject' => $userData['uri'],
                    'predicate' => PROPERTY_USER_LOGIN,
                    'object' => $userData[PROPERTY_USER_LOGIN]
                )
            );
        }
        $row++;
    }
    fclose($handle);
}
