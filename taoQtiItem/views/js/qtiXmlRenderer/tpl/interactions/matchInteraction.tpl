<{{tag}}{{#if attributes}} {{{join attributes '=' ' ' '"'}}}{{~/if~}}>
    {{~#if prompt}}{{{prompt}}}{{/if}}
    <simpleMatchSet>
        {{#matchSet1}}{{{.}}}{{/matchSet1}}
    </simpleMatchSet>
    <simpleMatchSet>
        {{#matchSet2}}{{{.}}}{{/matchSet2}}
    </simpleMatchSet>
</{{tag}}>