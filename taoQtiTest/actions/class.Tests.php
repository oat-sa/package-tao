<?php

/**
 * Tests Controller provides actions performed from url resolution
 *
 * @author Bertrand Chevrier, <bertrand@taoteting.com>
 * @package taoQtiTest
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoQtiTest_actions_Tests extends taoTests_actions_Tests {

    /**
     * Override the default options to add a property filter on QTI item model
     * @return array the options
     */
    protected function getItemsTreeOptions() {
        $options = parent::getItemsTreeOptions();
        if(empty($options['propertyFilter'])){
            $options['propertyFilter'] = array();
        }
        $options['propertyFilter'][TAO_ITEM_MODEL_PROPERTY] = TAO_ITEM_MODEL_QTI;
        return $options;
    }
}
?>
