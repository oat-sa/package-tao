/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 'lodash', 'i18n',
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/states/states',
    'taoQtiItem/qtiCommonRenderer/helpers/Graphic',
    'taoQtiItem/qtiCreator/helper/dummyElement'
], function($, _, __, Widget, states, graphic, dummyElement){

    /**
     * The Widget that provides components used by the QTI Creator for the Hotspot Interaction
     * @extends taoQtiItem/qtiCreator/widgets/interactions/Widget
     * @exports taoQtiItem/qtiCreator/widgets/interactions/hotspotInteraction/Widget
     */      
    var HotspotInteractionWidget = _.extend(Widget.clone(), {

        /**
         * Initialize the widget
         * @see {taoQtiItem/qtiCreator/widgets/interactions/Widget#initCreator}
         * @param {Object} options - extra options 
         * @param {String} options.baseUrl - the resource base url
         * @param {jQueryElement} options.choiceForm = a reference to the form of the choices
         */
        initCreator : function(options){
            this.baseUrl = options.baseUrl;
            this.choiceForm = options.choiceForm;
            
            this.registerStates(states);
            
            //call parent initCreator
            Widget.initCreator.call(this);
          
            this.createPaper(); 
        },

        /**
         * Gracefull destroy the widget
         * @see {taoQtiItem/qtiCreator/widgets/Widget#destroy}
         * @param {Object} options - extra options 
         * @param {String} options.baseUrl - the resource base url
         * @param {jQueryElement} options.choiceForm = a reference to the form of the choices
         */
        destroy : function(){

            var $container = this.$original;
            var $item      = $container.parents('.qti-item');

            //stop listening the resize
            $item.off('resize.gridEdit.' + this.element.serial);
            $(window).off('resize.qti-widget.' + this.element.serial);
            $container.off('resize.qti-widget.' + this.element.serial);

            //call parent destroy
            Widget.destroy.call(this);
        },

        /**
         * Create a basic Raphael paper with the interaction choices 
         */ 
        createPaper : function(){

            var $container  = this.$original;
            var $item       = $container.parents('.qti-item');
            var background  = this.element.object.attributes;
            var serial      = this.element.serial;
            if(!background.data){
                this._createPlaceholder();
            } else {
                this.element.paper = graphic.responsivePaper( 'graphic-paper-' + serial, serial, {
                    width       : background.width, 
                    height      : background.height,
                    img         : this.baseUrl + background.data,
                    imgId       : 'bg-image-' + serial,
                    container   : $container
                });

                //listen for internal size change
                $item.on('resize.gridEdit.' + serial, function(){
                    $container.trigger('resize.qti-widget.' + serial);
                });

                //call render choice for each interaction's choices
                _.forEach(this.element.getChoices(), this._currentChoices, this);
            }
        },

        /**
         * Creates a dummy placeholder if there is no image set
         */
        _createPlaceholder : function(){

            var $container = this.$original;
            var $imageBox  = $container.find('.main-image-box');
            dummyElement.get({
                icon: 'image',
                css: {
                    width  : $container.innerWidth() - 35,
                    height : 200
                },
                title : __('Select an image first.')
            }).appendTo($imageBox);
        },

        /**
         * Add shape to the Raphel paper for a QTI choice
         * @private
         * @param {Object} choice - the QTI choice 
         */ 
        _currentChoices : function(choice){
            graphic.createElement(this.element.paper, choice.attr('shape'), choice.attr('coords'), {
                id          : choice.serial,
                touchEffect : false
            });
        }
   });

    return HotspotInteractionWidget;
});
