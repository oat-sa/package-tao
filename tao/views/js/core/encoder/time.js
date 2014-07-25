define(['moment'], function(moment){
    
   var format = "HH:mm:ss";
    
   return {
       
       encode : function(modelValue){
           
           //seconds to hh:mm:ss
           var seconds = parseInt(modelValue, 10);
           if(isNaN(seconds)){
               seconds = 0;
           }
           var time = moment.duration(seconds, 'seconds');
           var h = time.get('hours') >= 10 ? time.get('hours') : '0' + time.get('hours');
           var m = time.get('minutes') >= 10 ? time.get('minutes') : '0' + time.get('minutes');
           var s = time.get('seconds') >= 10 ? time.get('seconds') : '0' + time.get('seconds');
           return  h + ':' + m + ':' + s;
       },
       
       decode : function(nodeValue){
           //hh:mm:ss to seconds
           var time  =  moment(nodeValue, format);
           return time.seconds() + (time.minutes() * 60) + (time.hours() * 3600);
       }
   };
});


