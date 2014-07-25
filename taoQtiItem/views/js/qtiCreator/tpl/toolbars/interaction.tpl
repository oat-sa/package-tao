<div class="tlb-wrapper" style="" data-edit="active" data-for="{{serial}}">
    <span class="tlb-title" title="{{title}}">{{title}}</span>
    <div class="tlb">
        <div class="rgt tlb-button" data-role="delete" title="{{__ 'delete'}}">
            <span class="icon-bin"></span>
        </div>
        {{#if switcher}}
        <div class="state-switcher">
            <span class="selected" data-state="question">{{__ "Question"}}</span>
            <span class="separator"> | </span>
            <span class="link" data-state="answer">{{__ "Response"}}</span>
        </div>
        {{/if}}
    </div>
</div>