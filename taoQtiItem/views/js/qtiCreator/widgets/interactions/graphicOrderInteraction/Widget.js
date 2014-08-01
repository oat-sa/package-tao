/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 'lodash', 'i18n',
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicInteraction/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/graphicOrderInteraction/states/states'
], function($, _, __, Widget, GraphicWidget, states){

    /**
     * The Widget that provides components used by the QTI Creator for the GraphicOrder Interaction
     *
     * @extends taoQtiItem/qtiCreator/widgets/interactions/Widget
     * @extends taoQtiItem/qtiCreator/widgets/interactions/GraphicInteraction/Widget
     *
     * @exports taoQtiItem/qtiCreator/widgets/interactions/graphicOrderInteraction/Widget
     */      
    var GraphicOrderInteractionWidget = _.extend(Widget.clone(), GraphicWidget, {

        /**
         * Initialize the widget
         * @see {taoQtiItem/qtiCreator/widgets/interactions/Widget#initCreator}
         * @param {Object} options - extra options 
         * @param {String} options.baseUrl - the resource base url
         * @param {jQueryElement} options.choiceForm = a reference to the form of the choices
         */
        initCreator : function(options){
            var paper;
            this.baseUrl = options.baseUrl;
            this.choiceForm = options.choiceForm;
            
            this.registerStates(states);
            
            //call parent initCreator
            Widget.initCreator.call(this);
           
            paper = this.createPaper(_.bind(this.scaleOrderList, this)); 
            if(paper){
                this.element.paper = paper;
                this.createChoices();
                this.renderOrderList();
            }
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
         * Called back on paper resize to scale the order list
         * @param {Number} newSize - the interaction size
         */
        scaleOrderList : function(newSize){
            $('ul.block-listing', this.$original).css('max-width', newSize + 'px');
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
