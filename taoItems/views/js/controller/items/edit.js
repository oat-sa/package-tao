
define(['module', 'layout/actions', 'jquery','helpers','ui/lock', 'ui/feedback', 'i18n'],
	function(module, actions, $, helpers, lock, feedback, __){

        var editItemController = {
            start : function(options){
                var config = module.config();
        
                var isPreviewEnabled = !!config.isPreviewEnabled;
                var isAuthoringEnabled = !!config.isAuthoringEnabled;
   
                var previewAction = actions.getBy('item-preview');
                var authoringAction = actions.getBy('item-authoring');
               
                if(previewAction){ 
                    previewAction.state.disabled = !config.isPreviewEnabled;
                }
                if(authoringAction){
                    authoringAction.state.disabled = !config.isAuthoringEnabled;
                }
                actions.updateState();
                
                $('#lock-box').each(function() {lock($(this)).register()});
            }
        };

        return editItemController;
});
