<div {{#if attributes.id}}id="{{attributes.id}}"{{/if}} class="qti-interaction qti-blockInteraction qti-mediaInteraction{{#if attributes.class}} {{attributes.class}}{{/if}}" data-serial="{{serial}}">
    {{#if prompt}}{{{prompt}}}{{/if}}
    <div class="instruction-container"></div>
    <div class="media-container"></div>
</div>
