<div class="textReaderInteraction qti-interaction">
    <div class="tr-wrap">
        <div class="tr-content js-page-container">
        </div>
        <div class="js-nav-container">
        </div>
    </div>
    <script class="text-reader-pages-tpl" type="text/x-handlebars-template">
        <![CDATA[
        <div class="tr-tabs js-page-tabs tr-tabs-\{{tabsPosition}} clearfix">
            \{{#if showTabs}}
            <ul class="tr-tab-buttons js-tab-buttons">
                \{{#each pages}}
                <li data-page-num="\{{@index}}" data-page-id="\{{id}}" class="tr-tab-buttons__item">
                    <span class="tr-tab-label">\{{inc @index}}</span>
                </li>
                \{{/each}}
            </ul>
            \{{/if}}     
            <div class="tr-pages-wrap clearfix">
                <div class="tr-pages" style="height: \{{pageWrapperHeight}}px">
                    \{{#each pages}}
                    <div data-page-num="\{{@index}}" data-page-id="\{{id}}" class="tr-page js-tab-content tr-tabs-\{{@index}}">
                        <div class="tr-passage" style="height: \{{../pageHeight}}px" >
                            \{{#each content}}
                            <div class="tr-passage-column widget-blockInteraction js-page-column" data-page-col-index="\{{@index}}">
                                \{{{this}}}
                            </div>
                            \{{/each}}
                        </div>
                    </div>
                    \{{/each}}
                </div>
            </div>
        </div>
        ]]>
    </script>    
    <script class="text-reader-nav-tpl" type="text/x-handlebars-template">    
        <![CDATA[
        \{{#if showNavigation}}
        <div class="tr-nav-wrap tr-nav-\{{tabsPosition}}">
            <div class="tr-nav">
                <div class="tr-nav__col js-prev-page">
                    <button class="btn-info small">\{{../buttonLabels.prev}}</button>
                </div>
                <div class="tr-nav__col">
                    {{__ "Page"}} <span class="js-current-page">\{{../currentPage}}</span> / \{{../pagesNum}}
                </div>
                <div class="tr-nav__col js-next-page">
                    <button class="btn-info small">\{{../buttonLabels.next}}</button>
                </div>
            </div>
        </div>
        \{{/if}}
        ]]>
    </script>
</div>