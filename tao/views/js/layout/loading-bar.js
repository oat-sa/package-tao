/**
 * @author Dieter Raber <dieter@taotesting.com>
 */
define(['jquery'],
    function ($) {

        var $loadingBar = $('.loading-bar'),
            originalHeight = $loadingBar.height(),
            $win = $(window),
            $doc = $(document),
            $contentWrap    = $('.content-wrap'),
            $versionWarning = $contentWrap.find('>.version-warning'),
            $header         = $contentWrap.find('header:first()'),
            $headerHeight;
        
        $win.on('scroll.loadingbar', function () {
            if(!$loadingBar.hasClass('loading')) {
                return;
            }
            $headerHeight = $versionWarning.length && $versionWarning.is(':visible')
                ? $header.height() + $versionWarning.height()
                : $header.height();

            if ($headerHeight <= $win.scrollTop()) {
                $loadingBar.addClass('fixed');
            }
            else {
                $loadingBar.removeClass('fixed');
            }
            $loadingBar.height($doc.height());
            if(!$versionWarning.length){
                $loadingBar.css('top', -28);
            }
        });

        return {
            start: function () {
                if($loadingBar.hasClass('loading')) {
                    $loadingBar.stop();
                }
                $loadingBar.addClass('loading');
                $win.trigger('scroll.loadingbar');
            },
            stop: function () {
                $loadingBar.removeClass('loading fixed').height(originalHeight);
            }
        };
    });