{{#each files}}
<li data-type-identifier="{{typeIdentifier}}">
    <span class="desc truncate">{{label}}</span>
    <div class="actions">
        <div class="tlb rgt">
            <div class="tlb-top">
                <span class="tlb-box">
                    <span class="tlb-bar">
                        <span class="tlb-start"></span>
                        <span class="tlb-group">
                            <a href="#" class="tlb-button-off" title="{{__ 'Remove this custom interaction'}}" data-delete=":parent li" data-delete-undo=false><span class="icon-bin"></span></a>
                        </span>
                        <span class="tlb-end"></span>
                    </span>  
                </span>   
            </div>
        </div>
    </div>
</li>
{{/each}}