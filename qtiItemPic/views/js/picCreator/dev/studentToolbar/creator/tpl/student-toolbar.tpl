<html5:div class="sts-scope">
    <html5:div id="sts-{{typeIdentifier}}" class="sts-container sts-{{typeIdentifier}}-container{{#if is.movable}} sts-movable-container{{/if}}{{#if is.transparent}} sts-transparent-container{{/if}}">
        <html5:div class="sts-title-bar">
            <html5:div class="sts-title">{{title}}</html5:div>
        </html5:div>
        <html5:div class="sts-content">
            <!-- Actual tools go here -->
        </html5:div>
        {{#if is.transmutable}}
        <html5:div class="sts-container-controls">
            {{#each is.rotatable}}
                {{#if this}}
                    <html5:div class="sts-handle-rotate-{{@key}}"></html5:div>
                {{/if}}
            {{/each}}
            {{#each is.adjustable}}
                {{#if this}}
                    <html5:div class="sts-handle-adjustable-{{@key}}"></html5:div>
                {{/if}}

            {{/each}}
        </html5:div>
        {{/if}}
    </html5:div>
</html5:div>