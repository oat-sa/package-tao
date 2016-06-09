<div class="datalist">
    <h1 {{#unless title}}class="hidden"{{/unless}}>{{title}}</h1>
    <h2>
        <span class="empty-list{{#unless textEmpty}} hidden{{/unless}}">{{textEmpty}}</span>
        <span class="available-list{{#unless textNumber}} hidden{{/unless}}"><span class="label">{{textNumber}}</span>: <span class="count"></span></span>
        <span class="loading{{#unless textLoading}} hidden{{/unless}}"><span>{{textLoading}}</span>...</span>
    </h2>
    <div class="list">
        {{#if tools}}
        <aside class="action-bar clearfix">
            {{#each tools}}
            <button class="btn-info small {{#if massAction}} mass-action hidden{{/if}}" data-control="{{id}}" {{#if title}} title="{{title}}"{{/if}}>
                {{#if icon}}<span class="icon icon-{{icon}}"></span>{{/if}}
                {{label}}
            </button>
            {{/each}}
        </aside>
        {{/if}}

        <table class="matrix">
            <colgroup>
                {{#if selectable}}
                <col/>
                {{/if}}
                <col/>
                {{#if actions}}
                <col/>
                {{/if}}
            </colgroup>
            <thead>
                <tr>
                    {{#if selectable}}
                    <th class="checkboxes"><input type="checkbox" name="checkall" value="1" /></th>
                    {{/if}}
                    <th class="label">{{labelText}}</th>
                    {{#if actions}}
                    <th class="actions">{{__ 'Actions'}}</th>
                    {{/if}}
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
