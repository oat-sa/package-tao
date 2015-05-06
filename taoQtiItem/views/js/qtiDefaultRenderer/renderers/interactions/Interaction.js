define(['taoQtiItem/qtiDefaultRenderer/widgets/Widgets'], function(Widgets){

    var Interaction = {
        render : function(interaction, data){

            var interactionName;
            var wwwPath = '';
            var graphicDebug = false;
            var runtimeContext = this.getOption('runtimeContext');
            
            if(runtimeContext){
                
                //use the global variable qti_base_www
                if(typeof(runtimeContext.runtime_base_www) !== 'undefined'){
                    wwwPath = runtimeContext.runtime_base_www;
                    if(!/\/$/.test(wwwPath) && wwwPath != ''){
                        wwwPath += '/';
                    }
                }

                interactionName = interaction.qtiClass.charAt(0).toUpperCase() + interaction.qtiClass.slice(1);
                if(typeof(runtimeContext.debug) !== 'undefined' && runtimeContext.debug){
                    graphicDebug = runtimeContext.debug;
                    if(!Widgets[interactionName]){
                        console.log("Error: Unknow widget " + interactionName);
                    }
                }
                
            }

            var context = {
                'wwwPath' : wwwPath,
                'graphicDebug' : graphicDebug,
            };
            interaction.widget = new Widgets[interactionName](interaction, context);
            interaction.widget.render();
        },
        setResponse : function(interaction, response){
            if(response !== null && typeof(response) !== 'undefined'){
                return interaction.widget.setResponse(response);
            }
        },
        getResponse : function(interaction){
            return interaction.widget.getResponse();
        }
    };

    return Interaction;
});
