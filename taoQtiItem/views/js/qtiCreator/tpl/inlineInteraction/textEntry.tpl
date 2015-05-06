<tr class="" data-edit="map">
    <td data-text><input type="text" name="text" value="{{text}}" data-old-text="{{text}}"/></td>
    <td class="mini-tlb" colspan="2">
        <label>
            <input name="correct" type="radio" {{#if correct}}checked="checked"{{/if}} />
            <span class="icon-radio"></span>
        </label>
        <input name="score" class="score" value="{{score}}" data-for="{{serial}}" data-validate="$numeric" data-validate-option="$allowEmpty; $event(type=keyup)"/>
        <span class="icon-bin" data-role="delete-option"></span>
    </td>
</tr>