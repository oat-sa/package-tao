oatbox-extension-installer
==========================

Custom composer installer for oatbox extension

It apply on composer package with type "tao-extension" and deploy the extension in folder define in extra as "tao-extension-name"

Here a sample composer.xml as an example

    {

        "name": "oat-sa/extension-tao-sample",
        "description" : "sample extension",
        "type" : "tao-extension",
        "extra" : {
        	"tao-extension-name" : "taoSampleFolder"
        },
        "minimum-stability" : "dev",
        "require": {
            "oat-sa/oatbox-extension-installer": "dev-master"
        }
    }
