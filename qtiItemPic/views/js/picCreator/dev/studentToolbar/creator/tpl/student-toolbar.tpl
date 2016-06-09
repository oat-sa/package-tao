<div class="sts-scope">
    <div id="sts-{{typeIdentifier}}" class="sts-container sts-{{typeIdentifier}}-container{{#if is.movable}} sts-movable-container{{/if}}{{#if is.transparent}} sts-transparent-container{{/if}}">
        <div class="sts-title-bar">
            <div class="sts-title">{{title}}</div>
        </div>
        <div class="sts-content">
            <!-- Actual tools go here -->
        </div>
        {{#if is.transmutable}}
        <div class="sts-container-controls">
            {{#each is.rotatable}}
                {{#if this}}
                    <div class="sts-handle-rotate-{{@key}}"></div>
                {{/if}}
            {{/each}}
            {{#each is.adjustable}}
                {{#if this}}
                    <div class="sts-handle-adjustable-{{@key}}"></div>
                {{/if}}

            {{/each}}
        </div>
        {{/if}}
    </div>
</div>