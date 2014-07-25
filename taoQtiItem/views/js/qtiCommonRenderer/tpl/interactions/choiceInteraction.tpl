<div class="qti-interaction qti-blockInteraction qti-choiceInteraction" data-serial="{{serial}}" data-qti-class="choiceInteraction">
  {{#if prompt}}{{{prompt}}}{{/if}}
  <div class="instruction-container"></div>
  <ol class="plain block-listing solid choice-area{{#if horizontal}} horizontal{{/if}}">
      {{#choices}}{{{.}}}{{/choices}}
  </ol>
  <div class="notification-container"></div>
</div>