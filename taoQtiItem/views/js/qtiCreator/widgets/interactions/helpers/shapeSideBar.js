/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 'lodash', 
    'tpl!taoQtiItem/qtiCreator/tpl/graphicInteraction/sidebar'
], function($, _, sidebarTmpl){

    /**
     * Helps you to create the side bar used to select shapes to draw in the QTI Create
     * @exports qtiCreator/widgets/interaction/helpers/shapeSideBar
     */
    var shapeSideBar  = {

        /**
         * Create the side bar and add it the container.
         * It will resize the container accordingly.
         * @param {jQueryElement} $container - a graphic interaction container
         * @param {Boolean} [showTarget = false] - if the target data type has to be shown
         * @returns {jQueryElement} the side bar element
         * @fires shapeSideBar#shapeactive.qti-widget 
         * @fires shapeSideBar#shapedeactive.qti-widget 
         * @fires resize.qti-widget
         */
        create : function create($container, showTarget){

            var $imageEditor = $container.find('.image-editor');
            var serial = $container.data('serial');
            var $imageBox = $('.main-image-box', $imageEditor);
            var $sideBar = $(sidebarTmpl({
                    showTarget : !!showTarget 
                 })).insertBefore($imageEditor);
            var $forms = $('li[data-type]', $sideBar);
            var $bin = $('li.bin', $sideBar);
            //var newWidth = $container.width() - $sideBar.outerWidth(true) - 2; //-2 is a security to prevent the element to dislpay below the sidebar
           
            /**
             * Set a form/shape into an active state
             * @param {jQueryElement} $form - the form/shape button
             */ 
            var activate = function activate($form){
                $forms.filter('.active').each(function(){
                    deactivate($(this));
                });
                $form.addClass('active');

                /**
                 * When a shape is activated 
                 * @event shapeSideBar#shapeactive.qti-widget
                 * @param {jQueryElement} $form - the shape element
                 * @param {String} type - the shape type
                 */
                $sideBar.trigger('shapeactive.qti-widget', [$form, $form.data('type')]);
            }; 
            
            /**
             * Set a form/shape into an inactive state
             * @param {jQueryElement} $form - the form/shape button 
             */ 
            var deactivate = function deactivate($form){
                $form.removeClass('active');
                
                /**
                 * A shape is deactivated 
                 * @event shapeSideBar#shapedeactive.qti-widget 
                 * @param {jQueryElement} $form - the shape element
                 * @param {String} type - the shape type
                 */
                $sideBar.trigger('shapedeactive.qti-widget', [$form, $form.data('type')]);
            }; 

            /**
             * To enable the bin 
             * @event shapeSideBar#enabalebin.qti-widget 
             */
            $sideBar.on('enablebin.qti-widget', function(){
               $bin.removeClass('disabled')
                    .on('click', function(e){
                        e.preventDefault();
                        $sideBar.trigger('bin.qti-widget');
                    });
            });
            
            /**
             * To disable the bin 
             * @event shapeSideBar#disabalebin.qti-widget 
             */
            $sideBar.on('disablebin.qti-widget', function(){
               $bin.addClass('disabled')
                   .off('click'); 
            });

            $forms.click(function(e){
                e.preventDefault();
                var $form = $(this);
                if(!$form.hasClass('active')){
                    activate($form);
                } else {
                    deactivate($form);
                }
            }); 
             
            $container.on('resize.qti-widget.' + serial, function(){
                _.defer(function(){
                    $sideBar.find('.forms').height($imageEditor.innerHeight());
                });
            });
            $container.trigger('resize.qti-widget.' + serial); 
            return $sideBar;
        },

        remove : function remove($container){
            var $sideBar = $('.image-sidebar', $container);
            var $imageEditor = $container.find('.image-editor');
            var $imageBox = $('.main-image-box', $imageEditor);
            if($sideBar.length){
                $sideBar.remove();
                //$imageBox.css('width', 'auto');
                //$imageEditor.css('width', 'auto');

                $container.off('resize.qti-widget.sidebar');
                $container.trigger('resize.qti-widget'); 
            }
        }
    };


    return shapeSideBar;
});
