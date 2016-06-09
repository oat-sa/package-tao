<div class="datatable-wrapper">

    {{#if options.filter}}
    <aside class="filter" data-column="{{options.filter.columns}}">
        <input type="text" value="" name="filter" placeholder="{{__ 'Filter'}}">
        <button class="icon-find" type="button"></button>
    </aside>
    {{/if}}

    {{#with options.status}}
    <h2>
        <span class="empty-list hidden">{{#if empty}}{{empty}}{{else}}{{__ 'Nothing to list!'}}{{/if}}</span>
        <span class="available-list hidden"><span class="text">{{#if available}}{{available}}{{else}}{{__ 'Available'}}{{/if}}</span>: <span class="count">{{count}}</span></span>
        <span class="loading">{{#if loading}}{{loading}}{{else}}{{__ 'Loading'}}{{/if}}...</span>
    </h2>
    {{/with}}

    {{#if options.tools}}
    <aside class="action-bar clearfix">
        {{#each options.tools}}
            <button class="btn-info small tool-{{#if id}}{{id}}{{else}}{{@key}}{{/if}}{{#if massAction}} invisible{{/if}}"{{#if title}} title="{{title}}"{{/if}}>
                <span class="icon-{{#if icon}}{{icon}}{{else}}{{#if id}}{{id}}{{else}}{{@key}}{{/if}}{{/if}}"></span> {{#if label}}{{label}}{{else}}{{#unless id}}{{@key}}{{/unless}}{{/if}}
            </button>
        {{/each}}
    </aside>
    {{/if}}

    <div class="grid-row clearfix pagination">
        <div class="col-6">{{__ 'Page'}} <strong>{{dataset.page}}</strong> {{__ 'of'}} {{dataset.total}}</div>
        <div class="col-6 txt-rgt">
            <button class="btn-info small datatable-backward"><span class="icon-backward"></span>{{__ 'Previous'}}</button>
            <button class="btn-info small datatable-forward">{{__ 'Next'}}<span class="icon-forward r"></span></button>
        </div>
    </div>

    <div class="datatable-container">
        <table class="matrix datatable">
            <colgroup>
                {{#if options.selectable}}
                <col/>
                {{/if}}
                {{#each options.model}}
                <col/>
                {{/each}}
            </colgroup>
            <thead>
                <tr>
                    {{#if options.selectable}}
                    <th class="checkboxes"><input type="checkbox" name="checkall" value="1" /></th>
                    {{/if}}
                    {{#each options.model}}
                    <th>
                        <div {{#if sortable}}data-sort-by="{{id}}"{{/if}}>{{label}}</div>
                        {{#if filterable}}
                        <aside class="filter column" data-column="{{id}}">
                            <input type="text" value="" name="filter" placeholder="{{#if filterable.placeholder}}{{filterable.placeholder}}{{else}}{{__ 'Filter'}}{{/if}}">
                            <button class="icon-find" type="button"></button>
                        </aside>
                        {{/if}}
                    </th>
                    {{/each}}
                    {{#if options.actions}}
                    <th class="actions">{{__ 'Actions'}}</th>
                    {{/if}}
                </tr>
            </thead>
            <tbody>
                {{#each dataset.data}}
                    <tr data-item-identifier="{{id}}">
                        {{#if ../options.selectable}}
                        <td class="checkboxes"><input type="checkbox" name="cb[{{id}}]" value="1" /></td>
                        {{/if}}
                        {{#each ../options.model}}
                            <td class="{{id}}">{{{property id ../this}}}</td>
                        {{/each}}
                        {{#if ../options.actions}}
                        <td class="actions">
                            {{#each ../../options.actions}}
                                {{#if id}}
                                    {{#with ../../this}}
                                        {{#unless ../hidden}}
                                            {{#with ../../this}}
                            <button class="btn-info small {{id}}"{{#if title}} title="{{title}}"{{/if}}><span class="icon-{{#if icon}}{{icon}}{{else}}{{id}}{{/if}}"></span> {{label}}</button>
                                            {{/with}}
                                        {{/unless}}
                                    {{/with}}
                                {{else}}
                            <button class="btn-info small {{@key}}"><span class="icon-{{@key}}"></span> {{@key}}</button>
                                {{/if}}
                            {{/each}}
                        </td>
                        {{/if}}
                    </tr>
                {{/each}}
            </tbody>
        </table>
    </div>
    <div class="grid-row clearfix pagination bottom">
        <div class="col-6">{{__ 'Page'}} <strong>{{dataset.page}}</strong> {{__ 'of'}} {{dataset.total}}</div>
        <div class="col-6 txt-rgt">
            <button class="btn-info small datatable-backward"><span class="icon-backward"></span>{{__ 'Previous'}}</button>
            <button class="btn-info small datatable-forward">{{__ 'Next'}}<span class="icon-forward r"></span></button>
        </div>
    </div>
</div>
