define([
    'ui/toggler', 
    'ui/disabler', 
    'ui/adder', 
    'ui/deleter', 
    'ui/incrementer', 
    'ui/inplacer', 
    'ui/btngrouper', 
    'ui/durationer',
    'ui/selecter',
    'ui/modal',
    'ui/tooltipster',
    'ui/form',
    'ui/validator',
    'ui/groupvalidator'
], function(toggler, disabler, adder, deleter, incrementer, inplacer, btngrouper, durationer, selecter, modal, tooltipster, form) {
    'use strict';
        
    /**
     * @author Bertrand Chevrier <bertrand@taotesting.com>
     * @exports ui
     */
     return {
         
        /**
         * Start up the components lookup and data-attr listening 
         * @param {jQueryElement} $container - to lookup within
         */
        startEventComponents : function($container){
            adder($container);
            btngrouper($container);
            deleter($container);
            disabler($container);
            toggler($container);
            inplacer($container);
            modal($container);
            form($container);
            this.startDomComponent($container);
        },
        
        startDomComponent : function($container){
            incrementer($container);
            durationer($container);
            selecter($container);
            tooltipster($container);
        }
    };
});
