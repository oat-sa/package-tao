{{~#if body~}}
<{{tag}}{{#if attributes}} {{{join attributes '=' ' ' '"'}}}{{/if}}>{{{body}}}</{{tag}}>
{{~else~}}
<{{tag}}{{#if attributes}} {{{join attributes '=' ' ' '"'}}}{{/if}} />
{{~/if~}}