define([
'tpl!taoQtiTest/controller/creator/templates/testpart',
'tpl!taoQtiTest/controller/creator/templates/section', 
'tpl!taoQtiTest/controller/creator/templates/rubricblock', 
'tpl!taoQtiTest/controller/creator/templates/itemref',
'tpl!taoQtiTest/controller/creator/templates/item',
'tpl!taoQtiTest/controller/creator/templates/test-props',
'tpl!taoQtiTest/controller/creator/templates/testpart-props', 
'tpl!taoQtiTest/controller/creator/templates/section-props', 
'tpl!taoQtiTest/controller/creator/templates/itemref-props', 
'tpl!taoQtiTest/controller/creator/templates/rubricblock-props'], 

function(testPart, section, rubricBlock, itemRef, item, testProps, testPartProps, sectionProps, itemRefProps, rubricBlockProps){
    'use strict';

    /**
     * Expose all the templates used by the test creator
     * @exports taoQtiTest/controller/creator/templates/index
     */
    return {
        'testpart'      : testPart,
        'section'       : section,
        'itemref'       : itemRef,
        'item'          : item,
        'rubricblock'   : rubricBlock,
        'properties'    : {
            'test'      : testProps,
            'testpart'  : testPartProps,
            'section'   : sectionProps,
            'itemref'   : itemRefProps,
            'rubricblock'   : rubricBlockProps
        }
    };
});
