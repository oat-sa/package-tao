<tr class="widget-box widget-inlineChoice qti-choice" data-edit="active" data-serial="{{serial}}">
    <td class="option"><div contenteditable="true">{{body}}</div></td>
    <td class="mini-tlb">
        <span data-edit="question" class="tlb-button">
            <span  class="icon-{{#if attributes.fixed}}pin{{else}}shuffle{{/if}}" data-role="shuffle-pin" style="{{#if interactionShuffle}}{{else}}display:none;{{/if}}"></span>
        </span>
        <label data-edit="map">
            <input name="correct" type="radio" value="{{serial}}">
            <span class="icon-radio"></span>
        </label>
    </td>
    <td class="mini-tlb">
        <span data-edit="question" class="icon-bin" data-role="delete"></span>
        <input class="score" name="score" data-edit="map" value="{{score}}" data-for="{{serial}}" data-validate="$numeric" data-validate-option="$allowEmpty; $event(type=keyup)"/>
    </td>
</tr>