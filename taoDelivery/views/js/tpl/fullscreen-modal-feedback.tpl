<div class="modal-feedback modal full-screen-modal">
    <div class="modal-body clearfix">
        <p class="message">
            {{__ "This test needs to be taken in full screen mode"}}
            {{#unless fsSupported}}({{launchButton}}){{/unless}}
        </p>
        <div class="rgt">
            {{#if fsSupported}}
            <button class="btn-info small enter-full-screen" type="button">{{__ "Enter full screen"}}</button>
            {{else}}
            <button class="btn-info small close-full-screen-prompt" type="button">{{__ "Close this prompt"}}</button>
            {{/if}}
        </div>
    </div>
</div>