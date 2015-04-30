<html5:div id="sts-{{typeIdentifier}}" class="sts-toolcontainer" data-position="{{position}}">
    <html5:span class="sts-button sts-launch-button" data-typeIdentifier="{{typeIdentifier}}" title="{{title}}">
        <html5:img src="{{icon}}" alt="{{alt}}" />
    </html5:span>
    <html5:div class="sts-container sts-hidden-container sts-{{typeIdentifier}}-container{{#if is.movable}} sts-movable-container{{/if}}{{#if is.transparent}} sts-transparent-container{{/if}}">
        <html5:div class="sts-title-bar">
            <html5:div class="sts-title">{{title}}</html5:div>
            <html5:ul class="sts-header-controls">
                <html5:li class="sts-close sts-button"></html5:li>
            </html5:ul>
        </html5:div>
        <html5:div class="sts-workspace">
            <html5:div class="sts-content">
                <!-- The template for {client}/{tool-title} goes here -->
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
</html5:div>