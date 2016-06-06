<tr>
    <td>{{label}}</td>
    <td>
        {{type}}
        <input type="hidden" name="users[{{user}}][type]" value="{{type}}">
    </td>
    <td>
        <label class="tooltip">
            <input type="checkbox" class="privilege-GRANT" name="users[{{user}}][GRANT]" value="1">
            <span class="icon-checkbox"></span>
        </label>
    </td>
    <td>
        <label class="tooltip">
            <input type="checkbox" class="privilege-WRITE" name="users[{{user}}][WRITE]" value="1" checked>
            <span class="icon-checkbox"></span>
        </label>
    </td>
    <td>
        <label class="tooltip">
            <input type="checkbox" class="privilege-READ" name="users[{{user}}][READ]" value="1" checked>
            <span class="icon-checkbox"></span>
        </label>
    </td>
    <td>
        <button type="button" class="small delete_permission tooltip btn-warning" data-acl-user="{{user}}" data-acl-type="{{type}}" data-acl-label="{{label}}" >
            <span class="icon-bin"></span>{{__ "Remove"}}
        </button>
    </td>
</tr>
