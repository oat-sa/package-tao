<?php

class taoQtiTest_actions_ItemRunner extends taoItems_actions_ItemRunner {
    
    protected function getResultServerApi() {
        $parentCallId = $this->getRequestParameter('QtiTestParentServiceCallId');
        $testDefinition = $this->getRequestParameter('QtiTestDefinition');
        $testCompilation = $this->getRequestParameter('QtiTestCompilation');
        return "new ResultServerApi('" . _url('', 'TestRunner','taoQtiTest') . "', '" . $parentCallId . "', '" . $testDefinition . "', '" . $testCompilation . "')";
    }
    
    protected function getResultServerApiPath() {
        return 'taoQtiTest/views/js/ResultServerApi.js';
    }
    
    protected function selectView() {
        $this->setView('runtime/item_runner.tpl', 'taoItems');
    }
    
    protected function selectWebFolder() {
        $this->setData('webFolder', ROOT_URL . 'taoItems/views/');
    }
}