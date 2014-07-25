/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 'lodash', 'i18n',
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicOrderInteraction/states/states',
    'taoQtiItem/qtiCommonRenderer/helpers/Graphic',
    'taoQtiItem/qtiCreator/helper/dummyElement'
], function($, _, __, Widget, states, graphic, dummyElement){

    /**
     * The Widget that provides components used by the QTI Creator for the GraphicOrder Interaction
     * @extends taoQtiItem/qtiCreator/widgets/interactions/Widget
     * @exports taoQtiItem/qtiCreator/widgets/interactions/graphicOrderInteraction/Widget
     */      
    var GraphicOrderInteractionWidget = _.extend(Widget.clone(), {

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
            var $itemBody   = $container.parents('.qti-itemBody');

            //stop listening the resize
            $itemBody.off('resizestop.gridEdit.' + this.element.serial);
            $(window).off('resize.qti-widget.' + this.element.serial);

            //call parent destroy
            Widget.destroy.call(this);
        },

        /**
         * Create a basic Raphael paper with the interaction choices 
         */ 
        createPaper : function(){
            var self = this;
            var interaction = this.element;
            var $container  = this.$original;
            var $itemBody   = $container.parents('.qti-itemBody');
            var $orderList  = $('ul.block-listing', $container);
            var background  = interaction.object.attributes;

            if(!background.data){
                this._createPlaceholder();
            } else {
            $(window).off('resize.qti');
                this.element.paper = graphic.responsivePaper( 'graphic-paper-' + this.element.serial, this.element.serial, {
                    width       : background.width, 
                    height      : background.height,
                    img         : this.baseUrl + background.data,
                    imgId       : 'bg-image-' + interaction.serial,
                    diff        : $('.image-editor', $container).outerWidth(true) - $('.main-image-box', $container).innerWidth(),
                    container   : $container,
                    resize      : function(newWidth){
                        var diff = $orderList.outerWidth(true) -$orderList.width();
                        var width = (newWidth < background.width ?  newWidth : background.width) - (diff > 0 ? diff : 0) + 8;
                        $orderList.width( width );
                    }
                });

                //listen for internal size change
                $itemBody.on('resizestop.gridEdit.' + interaction.serial, function(){
                    $container.trigger('resize.qti-widget');
                });

                //call render choice for each interaction's choices
                _.forEach(interaction.getChoices(), this._currentChoices, this);

                //creates the order list
                this.renderOrderList();

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
        },


        /**
         * Render the list of numbers
         */
         renderOrderList : function renderOrderList(){
            var interaction = this.element;
            var size        = _.size(interaction.getChoices());
            var min         = interaction.attr('minChoices');
            var max         = interaction.attr('maxChoices');
            var $orderList  = $('ul.block-listing', this.$original);

            //calculate the number of orderer to display
            if(max > 0 && max < size){
                size = max;
            } else if(min > 0 && min < size){
               size = min;
            }
    
            $orderList.empty();

            //add them to the list
            _.times(size, function(index){
                var position = index + 1;
                var $orderer = $('<li class="selectable" data-number="' + position + '">' + position +  '</li>');
                if(index === 0){
                    $orderer.addClass('active');
                }
                $orderList.append($orderer);
            });
        }
   });

    return GraphicOrderInteractionWidget;
});
