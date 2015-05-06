define(['uiBootstrap', 'helpers', 'uiForm', 'generis.actions', 'controller/main/toolbar'], 
    function (UiBootstrap, Helpers, UiForm, GenerisActions, toolbar) {

    return {
        start : function(){
            
            //initialize legacy components
            UiBootstrap.init();
            Helpers.init();
            UiForm.init();
            GenerisActions.init();
            
            //initialize main components
            toolbar.setUp();
        }
    };
});