<div {{#if attributes.id}}id="{{attributes.id}}"{{/if}} class="qti-interaction qti-blockInteraction qti-hottextInteraction{{#if attributes.class}} {{attributes.class}}{{/if}}" data-serial="{{serial}}" data-qti-class="hottextInteraction">
  {{#if prompt}}{{{prompt}}}{{/if}}
  <div class="instruction-container"></div>
  <div class="qti-flow-container">{{{body}}}</div>
</div>
