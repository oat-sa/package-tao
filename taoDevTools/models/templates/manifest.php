<?php
{licenseBlock}               

return array(
    'name' => '{id}',
	'label' => '{name}',
	'description' => '{description}',
    'license' => '{license}',
    'version' => '{version}',
	'author' => '{author}',
	'requires' => {requires},
	// for compatibility
	'dependencies' => {dependencies},
	'managementRole' => '{managementRole}',
    'acl' => array(
        array('grant', '{managementRole}', array('ext'=>'{id}')),
    ),
    'uninstall' => array(
    ),
    'autoload' => array (
        'psr-4' => array(
            '{authorNs}\\{id}\\' => dirname(__FILE__).DIRECTORY_SEPARATOR
        )
    ),
    'routes' => array(
        '/{id}' => '{authorNs}\\{id}\\controller'
    ),    
	'constants' => array(
	    # views directory
	    "DIR_VIEWS" => dirname(__FILE__).DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR,
	    
		#BASE URL (usually the domain root)
		'BASE_URL' => ROOT_URL.'{id}/',
	    
	    #BASE WWW required by JS
	    'BASE_WWW' => ROOT_URL.'{id}/views/'
	),
    'extra' => array(
        'structures' => dirname(__FILE__).DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'structures.xml',
    )
);