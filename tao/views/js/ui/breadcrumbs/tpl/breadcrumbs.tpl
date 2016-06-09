<ul class="breadcrumbs plain{{#if cls}} {{cls}}{{/if}}">
    {{#each breadcrumbs}}
    <li class="breadcrumb" data-breadcrumb="{{id}}">
        {{#if url}}
        <a href="{{url}}">{{label}}{{#if data}} - {{data}}{{/if}}</a>
        {{else}}
        <span class="a">{{label}}{{#if data}} - {{data}}{{/if}}</span>
        {{/if}}
        {{#if entries}}
        <ul class="entries plain">
            {{#each entries}}
            <li data-breadcrumb="{{id}}">
                <a href="{{url}}">{{label}}{{#if data}} - {{data}}{{/if}}</a>
            </li>
            {{/each}}
        </ul>
        {{/if}}
    </li>
    {{/each}}
</ul>
