define(['lodash'], function(_){
   return {
       encode : function(modelValue, glue){
           glue = glue || ',';
           return  _.isArray(modelValue) ? modelValue.join(glue) : modelValue;
       },
       
       decode : function(nodeValue, glue){
           glue = glue || ',';
           return nodeValue.split(glue);
       }
   };
});