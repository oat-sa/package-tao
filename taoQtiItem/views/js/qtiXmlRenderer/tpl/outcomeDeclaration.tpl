
<outcomeDeclaration{{#if attributes}} {{{join attributes '=' ' ' '"'}}}{{/if}}
{{~#if empty~}}
    />
{{else~}}
    >
    {{#if defaultValue.length ~}}
    <defaultValue>
        {{#each defaultValue ~}}
        <value>{{.}}</value>
        {{/each ~}}
    </defaultValue>
    {{/if ~}}
    </outcomeDeclaration>
{{~/if ~}}
