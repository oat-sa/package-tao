<div class="tr-tabs js-page-tabs tr-tabs-{{tabsPosition}} clearfix">
    {{#if showTabs}}
    <ul class="tr-tab-buttons js-tab-buttons">
        {{#each pages}}
        <li data-page-num="{{@index}}" data-page-id="{{id}}" class="tr-tab-buttons__item">
            <span class="tr-tab-label">{{inc @index}}</span>
            {{#if ../showRemovePageButton}}
            <span class="js-remove-page tr-close-tab icon icon-bin" data-page-num="{{@index}}" title="{{__ "Delete"}}"></span>
            {{/if}}
        </li>
        {{/each}}
    </ul>
    {{/if}}     

    <div class="tr-pages-wrap clearfix">
        <div class="tr-pages" style="height: {{pageWrapperHeight}}px">
            
            {{#if authoring}}
            <div class="add-option js-add-page-before">
                <span class="icon-add"></span>
                {{__ "Add page"}}
            </div>
            {{/if}}

            {{#each pages}}
            <div data-page-num="{{@index}}" data-page-id="{{id}}" class="tr-page js-tab-content tr-tabs-{{@index}}">
                {{#if ../authoring}}
                <label class="tr-column-select">
                    {{__ "Columns:"}}
                    <select class="js-page-columns-select">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </label>
                {{#if ../../showRemovePageButton}}
                <span class="icon-bin js-remove-page" data-page-num="{{@index}}" title="{{__ "Delete"}}"></span>
                {{/if}}
                {{/if}}
                <div class="tr-passage" style="height: {{../pageHeight}}px" >
                    {{#each content}}
                    <div class="tr-passage-column widget-blockInteraction js-page-column" data-page-col-index="{{@index}}">
                        {{{this}}}
                    </div>
                    {{/each}}
                </div>
            </div>
            {{/each}}

            {{#if authoring}}
            <div class="add-option js-add-page-after">
                <span class="icon-add"></span>
                {{__ "Add page"}}
            </div>
            {{/if}}
            
        </div>
    </div>
</div>