define(function(){
   return {
       encode : function(modelValue){
           return  modelValue === true ? 'true' : 'false';
       },
       
       decode : function(nodeValue){
           return (nodeValue === 'true');
       }
   };
});