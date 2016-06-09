<div {{#if attributes.id}}id="{{attributes.id}}"{{/if}} class="qti-interaction qti-blockInteraction qti-choiceInteraction{{#if attributes.class}} {{attributes.class}}{{/if}}" data-serial="{{serial}}" data-qti-class="choiceInteraction">
  {{#if prompt}}{{{prompt}}}{{/if}}
  <div class="instruction-container"></div>
  <ol class="plain block-listing solid choice-area{{#if horizontal}} horizontal{{/if}} {{#if listStyle}}{{{listStyle}}}{{/if}}">
      {{#choices}}{{{.}}}{{/choices}}
  </ol>
  <div class="notification-container"></div>
</div>
