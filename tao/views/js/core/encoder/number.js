define(function(){
   return {
       encode : function(modelValue){
           return modelValue + '';
       },
       
       decode : function(nodeValue){
           return parseInt(nodeValue, 10);
       }
   } 
});


