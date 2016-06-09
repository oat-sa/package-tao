<div {{#if attributes.id}}id="{{attributes.id}}"{{/if}} class="qti-interaction qti-blockInteraction qti-graphicInteraction qti-graphicGapMatchInteraction clearfix{{#if attributes.class}} {{attributes.class}}{{/if}}" data-serial="{{serial}}">
    {{#if prompt}}{{{prompt}}}{{/if}}
    <div class="instruction-container"></div>
    <div class="image-editor solid">
        <div id='graphic-paper-{{serial}}' class="main-image-box"></div>
        <div class="clearfix"></div>
        <ul class="none block-listing horizontal source">
            {{#gapImgs}}{{{.}}}{{/gapImgs}}
        </ul>
    </div>
</div>
