/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['layout/section'], function(section) {
    'use strict';
       
    /**
     * Ensure edit section is disabled
     * @exports controller/users/disable-edit
     */    
    return {
        start : function(){
            section.get('edit_user').disable();
        }
    };
});
