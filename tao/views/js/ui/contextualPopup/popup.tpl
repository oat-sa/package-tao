<div class="contextual-popup" style="top:{{popup.top}}px; left:{{popup.left}}px">
    <div class="arrow" style="left:{{arrow.left}}px"></div>
    <div class="arrow-cover" style="left:{{arrow.leftCover}}px"></div>
    <div class="popup-content">{{{content}}}</div>
    <div class="footer">
        {{#if controls}}
            {{#if controls.done}}<button class="btn btn-info small done">done</button>{{/if}}
            {{#if controls.cancel}}<a href="#" class="btn cancel" title="{{__ "cancel"}}">cancel</a>{{/if}}
        {{/if}}
    </div>
</div>