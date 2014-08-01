<{{tag}}{{#if attributes}} {{{join attributes '=' ' ' '"'}}}{{~/if~}}>
    {{~#if prompt}}{{{prompt}}}{{/if}}
    {{~#if object}}{{{object}}}{{/if}}
    {{~#choices}}{{{.}}}{{/choices}}
    {{~#if body}}{{{body}}}{{/if}}
</{{tag}}>