<graphicGapMatchInteraction{{#if attributes}} {{{join attributes '=' ' ' '"'}}}{{/if}}>
    {{~#if prompt}}{{{prompt}}}{{/if}}
    {{{object}}}
    {{~#gapImgs}}{{{.}}}{{/gapImgs}}
    {{~#choices}}{{{.}}}{{/choices}}
</graphicGapMatchInteraction>