/**
 *
 * FIXME I am an util, so please move me into the util folder
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['jquery'], function($){
    'use strict';


    var ns = 'history';

    /**
     * Browser history management
     * @exports core/history
     */
    var history = {

        /**
         * Some browsers have the backspace button configured to run window.history.back(); we will fix this awefull behavior.
         * The strategy is to prevent backspace everywhere except in text and editable elements.
         */
        fixBrokenBrowsers : function fixBrokenBrowsers(){

            //to be completed if needed
            var enabledSelector = [
                'input',
                'textarea',
                '[contenteditable=true]'
            ].join(',');

            var preventBackSpace = function preventBackSpace(e){
                return (e.keyCode !== 8);
            };
            var preventBackSpacePropag = function preventBackSpacePropag(e){
                if(e.keyCode === 8 && !e.target.readonly && !e.target.disbaled){
                    e.stopPropagation();
                }
                return true;
            };
            $(document).off('.' + ns);
            $(document).off('.' + ns, enabledSelector);

            $(document).on('keydown.' + ns, preventBackSpace);
            $(document).on('keypress.' + ns, preventBackSpace);
            $(document).on('keydown.' + ns, enabledSelector, preventBackSpacePropag);
            $(document).on('keypress.' + ns, enabledSelector, preventBackSpacePropag);
        }
    };

    return history;
});
