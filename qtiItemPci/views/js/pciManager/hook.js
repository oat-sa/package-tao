define([
    'jquery',
    'i18n',
    'taoQtiItem/qtiCreator/editor/interactionsToolbar',
    'qtiItemPci/pciManager/pciManager',
    'tpl!qtiItemPci/pciManager/tpl/managerTrigger',
    'css!qtiItemPciCss/pci-manager'
], function($, __, interactionsToolbar, PciManager, triggerTpl){

    function addManagerButton(config){

        var $interactionSidebar = config.dom.getInteractionToolbar();

        //get the custom interaction section in the toolbar
        var customInteractionTag = interactionsToolbar.getCustomInteractionTag();
        var $section = interactionsToolbar.getGroup($interactionSidebar, customInteractionTag);
        if(!$section.length){
            //no custom interaction yet, add a section
            $section = interactionsToolbar.addGroup($interactionSidebar, customInteractionTag);
        }

        //add button
        var $button = $(triggerTpl({
            title : __('Manage custom interactions')
        }));
        $section.children('.panel').append($button);

        var pciManager = new PciManager({
            container : config.dom.getModalContainer(),
            interactionSidebar : $interactionSidebar,
            itemUri : config.uri
        });
        $button.on('click', function(){
            pciManager.open();
        });
    }

    var pciManagerHook = {
        init : function(config){

            var $interactionSidebar = config.dom.getInteractionToolbar();

            if(interactionsToolbar.isReady($interactionSidebar)){
                addManagerButton(config);
            }else{
                //wait until the interaction toolbar construciton is done:
                $interactionSidebar.on('interactiontoolbarready.qti-widget', function(){
                    addManagerButton(config);
                });
            }
        }
    };

    return pciManagerHook;
});
