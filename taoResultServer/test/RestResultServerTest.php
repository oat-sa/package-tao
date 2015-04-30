<?php
namespace oat\taoResultServer\test;

use oat\tao\test\RestTestCase;

class RestResultServerTest extends RestTestCase
{
    public function serviceProvider(){
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoResultServer');
        return array(
            array('taoResultServer/RestResultServer',TAO_RESULTSERVER_CLASS)
        );
    }
    

}