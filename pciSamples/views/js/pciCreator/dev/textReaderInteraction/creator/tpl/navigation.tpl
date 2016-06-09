{{#if showNavigation}}
<div class="tr-nav-wrap tr-nav-{{tabsPosition}}">
    <div class="tr-nav">
        <div class="tr-nav__col js-prev-page">
            <button class="btn-info small">{{../buttonLabels.prev}}</button>
        </div>
        <div class="tr-nav__col">
            {{__ "Page"}} <span class="js-current-page">{{../currentPage}}</span> / {{../pagesNum}}
        </div>
        <div class="tr-nav__col js-next-page">
            <button class="btn-info small">{{../buttonLabels.next}}</button>
        </div>
    </div>
</div>
{{/if}}