<div id="{{serial}}" data-serial="{{serial}}" data-identifier="{{attributes.identifier}}" class="{{#unless inline}}modal {{/unless}}qti-modalFeedback {{feedbackStyle}}">
    {{#if attributes.title}}<h2 class="qti-title modal-title">{{attributes.title}}</h2>{{/if}}
    <div class="modal-body">{{{body}}}</div>
</div>