<{{ns}}properties{{#if key}} key="{{key}}"{{/if}}>
    {{#each entries}}
        {{~#if key~}}
            <{{../../ns}}entry key="{{key}}">{{value}}</{{../../ns}}entry>
        {{else}}
            {{{value}}}
        {{~/if~}}
    {{/each}}
</{{ns}}properties>