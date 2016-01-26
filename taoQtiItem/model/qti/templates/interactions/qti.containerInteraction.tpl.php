<?php
/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
?>
<<?=get_data('tag')?> <?=get_data('attributes')?>>
<?php if(!is_null(get_data('prompt'))):?>
    <?=get_data('prompt')?>
<?php endif?>
<?php if(!is_null(get_data('choices'))):?>
    <?=get_data('choices')?>
<?php endif?>
<?php if(!is_null(get_data('body'))):?>
    <?=get_data('body')?>
<?php endif?>
</<?=get_data('tag')?>>