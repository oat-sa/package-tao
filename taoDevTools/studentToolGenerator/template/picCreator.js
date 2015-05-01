define([
    'lodash',
    'taoQtiItem/qtiCreator/editor/infoControlRegistry',
    '{tool-id}/creator/widget/Widget',
    'tpl!{tool-id}/creator/tpl/{tool-base}'
], 
function(_, registry, Widget, markupTpl){


    /**
     * Retrieve data from manifest
     */
    var manifest = registry.get('{tool-id}').manifest;

    /**
     * Configuration of the container
     *
     * t/r/b/l is meant to be an alternative to the tl/tr/br/bl syntax.
     * Using them together might look rather weird.
     */
    var is = {
        transparent: {is-transparent}, 
        movable: {is-movable}, 
        rotatable: {
            tl: {is-rotatable-tl},
            tr: {is-rotatable-tr},
            bl: {is-rotatable-bl},
            br: {is-rotatable-br},
            t:  {is-rotatable-t},
            r:  {is-rotatable-r},
            b:  {is-rotatable-b},
            l:  {is-rotatable-l}
        },
        adjustable: {
            x:  {is-adjustable-x}, 
            y:  {is-adjustable-y}, 
            xy: {is-adjustable-xy}
        } 
    };
    is.transmutable = _.some(is.rotatable, Boolean) || _.some(is.adjustable, Boolean);

    // the position in which the checkbox should appear
    var position = 100;

    return {
        /**
         * (required) Get the typeIdentifier of the custom interaction
         * 
         * @returns {String}
         */
        getTypeIdentifier : function(){
            return manifest.typeIdentifier;
        },
        /**
         * (required) Get the widget prototype
         * Used in the renderer
         * 
         * @returns {Object} Widget
         */
        getWidget : function(){
            return Widget;
        },
        /**
         * (optional) Get the default properties values of the PIC.
         * Used on new PIC instance creation
         * 
         * @returns {Object}
         */
        getDefaultProperties : function(pic){
            return {
                is: is,
                position: position
            };
        },

        /**
         * (optional) Callback to execute on the 
         * Used on new pic instance creation
         * 
         * @returns {Object}
         */
        afterCreate : function(pic){
            //do some stuff
        },

        /**
         * (required) Returns the QTI PIC XML template 
         * 
         * @returns {function} handlebar template
         */
        getMarkupTemplate : function(){
            return markupTpl;
        },

        /**
         * (optional) Allows passing additional data to xml template
         * 
         * @returns {function} handlebar template
         */
        getMarkupData : function(pic, defaultData){
            
            defaultData = _.defaults(defaultData, {
                typeIdentifier : manifest.typeIdentifier,
                title : manifest.label,
                is: is,
                position: position,
                //referenced as a required file in manifest.media[]
                icon : manifest.typeIdentifier + '/runtime/media/{tool-base}-icon.svg',
                alt : manifest.short || manifest.label
            });
            
            return defaultData;
        }
    };
});