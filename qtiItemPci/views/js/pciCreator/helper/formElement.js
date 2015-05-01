define([], function(){

    var formElement = {
        /**
         * the simplest form of save callback used in taoQtiItem/qtiCreator/widgets/helpers/formElement.setChangeCallbacks()
         * @param {boolean} allowEmpty
         */
        getPropertyChangeCallback : function(allowEmpty){

            return function(element, value, name){
                if(!allowEmpty && value === ''){
                    element.removeProp(name);
                }else{
                    element.prop(name, value);
                }
            }
        }
    };

    return formElement;
});
