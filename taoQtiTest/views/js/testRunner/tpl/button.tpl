<li data-control="{{id}}" class="small btn-info action btn-{{type}}{{#if is.button}} action-button{{/if}}{{#if active}} active{{/if}}"{{#if title}} title="{{title}}"{{/if}} data-order="{{order}}">
    {{#if is.button}}
    <a class="li-inner" href="#">
        {{#if icon}}<span class="icon icon-{{icon}}{{#unless label}} no-label{{/unless}}"></span>{{/if}}
        {{#if label}}<span class="label">{{label}}</span>{{/if}}
    </a>
    {{/if}}
    {{#if is.menu}}
    <ul class="menu hidden">
        {{#each ../items}}
        <li data-control="{{id}}" class="small btn-info action menu-item{{#if selected}} selected{{/if}}"{{#if title}} title="{{title}}"{{/if}}>
            <a class="li-inner menu-inner" href="#">
                {{#if icon}}<span class="icon icon-{{icon}}{{#unless label}} no-label{{/unless}}"></span>{{/if}}
                {{#if label}}<span class="label">{{label}}</span>{{/if}}
            </a>
        </li>
        {{/each}}
    </ul>
    {{/if}}
    {{#if is.group}}
        {{#each ../items}}
        <a data-control="{{id}}" class="li-inner action-button{{#if active}} active{{/if}}" href="#"{{#if title}} title="{{title}}"{{/if}}>
            {{#if icon}}<span class="icon icon-{{icon}}{{#unless label}} no-label{{/unless}}"></span>{{/if}}
            {{#if label}}<span class="label">{{label}}</span>{{/if}}
        </a>
        {{/each}}
    {{/if}}
    {{{content}}}
</li>
