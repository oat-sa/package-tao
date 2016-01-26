<div class="image-sidebar">
    <ul class="none forms">
        <li data-type="rect"><span title="{{__ 'Draw a rectangle on the image'}}" class="icon-rectangle"></span></li>
        <li data-type="circle"><span title="{{__ 'Draw a circle on the image'}}" class="icon-circle"></span></li>
        <li data-type="ellipse"><span title="{{__ 'Draw an ellipsis on the image'}}" class="icon-ellipsis"></span></li>
        <li data-type="path"><span title="{{__ 'Draw a free form on the image'}}" class="icon-free-form"></span></li>
    
        {{#if showTarget}}
        <li class="separator"></li>
        <li data-type="target"><span title="{{__ 'Drop the target to select a point'}}" class="icon-target"></span></li>
        {{/if}}

        <li class="separator"></li>
        <li class="bin disabled"><span title="{{__ 'Remove the selected shape'}}" class="icon-bin"></span></li>
    </ul>
</div>
