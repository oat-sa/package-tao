<div class="feedback-info popup" data-for="{{serial}}">
    <span class="icon-info"></span>
    {{#equal count 1}}
        {{__ "You have deleted an element"}}.
    {{else}}
        {{__ "You have deleted"}} {{count}} {{__ "elements"}}.
    {{/equal}}
    <a class="undo" href="#">{{__ "undo"}}</a>
    <span title="{{__ "Remove Message"}}" class="icon-close close-trigger"></span>
</div>