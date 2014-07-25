define(['jquery' ,'class', 'mediaElement'], function($, Class, MediaElementPlayer){

    var QtiObject = Class.extend({
        init : function(obj, context){
            
            this.playCounter = 0;
            this.mediaType = obj.getMediaType();

            if(this.mediaType === 'video' || this.mediaType === 'audio'){

                var _this = this;

                this.playerOptions = {
                    loop : false,
                    maxPlays : 0,
                    autostart : false,
                    pauseOtherPlayers : false,
                    features : ['playpause', 'current', 'duration', 'progress', 'volume'],
                    pluginPath : MediaElementPlayer.pluginPath,
                    audioWidth : 300
                };

                //set height and width if defined properly
                var width = parseInt(obj.attr('width'));
                if(width > 10){
                    if(this.mediaType === 'video'){
                        this.playerOptions.videoWidth = width;
                    }else{
                        this.playerOptions.audioWidth = width;
                    }
                }

                var height = parseInt(obj.attr('height'));
                if(height > 10){
                    if(this.mediaType === 'video'){
                        this.playerOptions.videoHeight = height;
                    }else{
                        this.playerOptions.audioHeight = height;
                    }
                }

                this.id = obj.getSerial();
                this.media = null;

                if(context){
                    
                    this.pluginPath = context.pluginPath || '';
                    if(this.pluginPath){
                        this.playerOptions.pluginPath = this.pluginPath;
                    }
                    if(typeof(context.playerOptions) === 'object'){

                        this.playerOptions = $.extend(this.playerOptions, context.playerOptions);

                        //overwrite the success callback:
                        var successCallback = (typeof(context.playerOptions.success) === 'function') ? context.playerOptions.success : function(){
                        };

                        this.playerOptions.success = function(mediaElement, domObject){

                            setTimeout(function(){

                                var controlMaxPlays = function(){
                                    if(_this.playerOptions.maxPlays && _this.playCounter >= _this.playerOptions.maxPlays){
                                        //completely stop playback:
                                        _this.stop();
                                    }
                                };

                                mediaElement.addEventListener('ended', function(e){
                                    _this.playCounter++;
                                    controlMaxPlays();
                                }, false);

                                mediaElement.addEventListener('play', controlMaxPlays);
                                mediaElement.addEventListener('pause', function(){
                                });


                                if(_this.playerOptions.autostart){
                                    mediaElement.play();

                                    //ie8 hack:
                                    setTimeout(function(){
                                        $(domObject).parent('div.mejs-mediaelement').siblings('div.mejs-layers').find('div.mejs-overlay-button').click().mousedown();
                                    }, 1000);
                                }

                                successCallback(mediaElement, domObject);

                            }, 300);//timeout to fix youtube auto start issue

                        };

                    }
                }

            }

        },
        render : function(){
            if(this.mediaType === 'video' || this.mediaType === 'audio'){
                this.media = new MediaElementPlayer('#' + this.id, this.playerOptions);
            }
        },
        playCount : function(count){
            if(isNaN(count)){
                return this.playCounter;
            }else{
                this.playCounter = count;
            }
        },
        pause : function(){
            this.media.pause();
        },
        stop : function(){
            this.pause();
            //change play button
            $('#' + this.id).parent('div.mejs-mediaelement').siblings('div.mejs-layers').find('div.mejs-overlay-button').hide();
        }
    });
    
    return QtiObject;
});