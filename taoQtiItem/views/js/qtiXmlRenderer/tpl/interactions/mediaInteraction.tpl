<{{tag}}{{#if attributes}} {{{join attributes '=' ' ' '"'}}}{{~/if~}}>
    {{~#if prompt}}{{{prompt}}}{{/if}}
    <object {{#if object.attributes}} {{{join object.attributes '=' ' ' '"'}}}{{/if}} />
    {{~#choices}}{{{.}}}{{/choices}}
    {{~#if body}}{{{body}}}{{/if}}
</{{tag}}>