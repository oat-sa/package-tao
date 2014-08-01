define(['taoQtiItem/qtiDefaultRenderer/widgets/Widget', 'taoQtiItem/qtiDefaultRenderer/widgets/Object'], function(Widget, Object){

    var MediaInteraction = Widget.extend({
        init : function(interaction, context){
            this._super(interaction, context);

            var autostart = interaction.attr('autostart') ? interaction.attr('autostart') : false;
            var context = {
                'pluginPath' : context.pluginPath || '',
                'playerOptions' : {
                    'loop' : interaction.attr('loop') ? interaction.attr('loop') : false,
                    'maxPlays' : interaction.attr('maxPlays') ? interaction.attr('maxPlays') : false,
                    'autostart' : autostart,
                    'features' : ['current', 'duration', 'volume'], //always overwrite features to disable control on evaluation mode
                    'clickToPlayPause' : false, //in evaluation mode, prevent test taker to be able to control the playback
                    'success' : function(media, dom){

                        $(dom).parent('div.mejs-mediaelement').siblings('div.mejs-layers').find('div.mejs-overlay-button').one('mousedown', function(){
                            media.play();
                        });

                        var disablePlay = function(){

                            media.addEventListener('pause', function(){
                                media.play();
                            });

                            //for video files
                            $(dom).parent('div.mejs-mediaelement').siblings('div.mejs-controls').find('div.mejs-playpause-button').remove();

                            //for youtube videos:
                            if(!$(dom).siblings('div.mejs-youtube-overlay').length){
                                var w = $('#' + media.id).width();
                                var h = $('#' + media.id).height();
                                $('<div class="mejs-youtube-overlay"></div>').insertAfter(dom).css('width', w).css('height', h).on('click', function(e){
                                    e.preventDefault();
                                    e.stopPropagation();
                                    e.returnValue = false;
                                    return false;
                                });
                            }

                        };

                        //on play, remove all control:
                        media.addEventListener('play', disablePlay);

                    }
                }
            }

            if(!context.playerOptions.autostart){
                context.playerOptions.features.unshift('playpause');//provide a way for the test taker to start a media playback when it does not start automatically
            }
            this.objectWidget = new Object(interaction.getObject(), context);
        },
        render : function(){
            this.objectWidget.render();
        },
        setResponse : function(values){
            this.objectWidget.playCount(parseInt(values));
        },
        getResponse : function(){
            return {
                "identifier" : this.options.responseIdentifier,
                "value" : this.objectWidget.playCount()
            };
        }
    });

    return MediaInteraction;
});