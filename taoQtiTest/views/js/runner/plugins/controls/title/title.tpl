<div class="title-box truncate">
    {{#each titles}}
        <span data-control="{{control}}" class="qti-controls" title="{{text}}">{{#unless @first}} - {{/unless}}{{text}}</span>
    {{/each}}
</div>
