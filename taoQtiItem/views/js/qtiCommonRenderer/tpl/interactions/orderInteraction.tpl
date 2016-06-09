<div {{#if attributes.id}}id="{{attributes.id}}"{{/if}} class="qti-interaction qti-blockInteraction qti-orderInteraction{{#if horizontal}} qti-horizontal{{else}} qti-vertical{{/if}}{{#if attributes.class}} {{attributes.class}}{{/if}}" 
     data-serial="{{serial}}" 
     data-qti-class="orderInteraction" 
     data-orientation="{{#if horizontal}}horizontal{{else}}vertical{{/if}}">
    {{#if prompt}}{{{prompt}}}{{/if}}
    <div class="instruction-container"></div>
    <div class="order-interaction-area">
        <ul class="choice-area square source solid block-listing {{#if horizontal}}horizontal{{/if}}">
            {{#choices}}{{{.}}}{{/choices}}
        </ul>
        <div class="arrow-bar middle">
            <span class="icon-add-to-selection {{#if horizontal}}icon-down{{else}}icon-right{{/if}}"></span>
            <span class="icon-remove-from-selection {{#if horizontal}}icon-up{{else}}icon-left{{/if}} inactive"></span>
        </div>
        <ul class="result-area decimal target solid block-listing {{#if horizontal}}horizontal{{/if}}">
        </ul>
        <div class="arrow-bar">
            <span class="icon-move-before {{#if horizontal}}icon-left{{else}}icon-up{{/if}} inactive"></span>
            <span class="icon-move-after {{#if horizontal}}icon-right{{else}}icon-down{{/if}} inactive"></span>
        </div>
    </div>
    <div class="notification-container"></div>
</div>
