<?php
require_once (dirname(__FILE__) . '/../../QtiSmTestCase.php');

use qtism\data\ItemSessionControl;

class ItemSessionControlTest extends QtiSmTestCase {
	
    public function testIsDefault() {
        $itemSessionControl = new ItemSessionControl();
        $this->assertTrue($itemSessionControl->isDefault());
        
        $itemSessionControl->setMaxAttempts(0);
        $this->assertFalse($itemSessionControl->isDefault());
    }
}