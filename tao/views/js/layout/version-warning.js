define([
    'jquery',
    'jquery.cookie'
],
    function($){

        var versionWarning = $('.version-warning');

        /**
         * Hide the warning and add a class to <html>
         *
         * @param slide
         */
        function hideWarning(slide) {

            var callback = function() {
                document.documentElement.className += ' no-version-warning';
                versionWarning.trigger('hiding.versionwarning');
            };

            if(!slide) {
                versionWarning.hide();
                callback();
            }
            else {
                versionWarning.slideUp('slow', function() {
                    versionWarning.slideUp('slow', callback);
                });
            }
        }

    return {
        /**
         * Initialize behaviour of version warning
         */
        init : function(){
            if($.cookie('versionWarning')) {
                hideWarning(false);
                return;
            }

            versionWarning.find('.close-trigger').on('click', function() {
                $.cookie('versionWarning', true, { path: '/' });
                hideWarning(true);
            });

        }
    };
});


