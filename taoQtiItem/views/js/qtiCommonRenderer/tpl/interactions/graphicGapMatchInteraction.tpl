<div class="qti-interaction qti-blockInteraction qti-graphicInteraction qti-graphicGapMatchInteraction {{attributes.class}}" data-serial="{{serial}}">
    {{#if prompt}}{{{prompt}}}{{/if}}
    <div class="instruction-container"></div>
    <div class="image-editor solid">
        <div id='graphic-paper-{{serial}}' class="main-image-box"></div>
        <div class="clear"></div>
        <ul class="none block-listing solid horizontal source">
            {{#gapImgs}}{{{.}}}{{/gapImgs}}
        </ul>
    </div>
</div>
