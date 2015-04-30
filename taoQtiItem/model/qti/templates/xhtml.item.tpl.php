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
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?=get_data('title')?></title>

        <style>
            #qti-preview-view-options{padding:10px;border:1px solid #ddd;background:rgba(238,238,238,0.8);color:#333;}
            #qti-preview-view-options ul{margin:0;}
            #qti-preview-view {font-weight: bold;}
            #qti-preview-view:hover{opacity:0.5;cursor:pointer;}
            .qti-view-option{text-decoration: underline;color:blue;cursor:pointer;}
            .qti-view-option:hover{opacity:0.5;}
            .qti-view-selected{text-decoration: none;color:black;}
        </style>

        <script id="initQtiRunner" type="text/javascript">
            (function(){
                window.tao = window.tao || {};
                window.tao.qtiRunnerContext = {
                    itemData : <?=json_encode(get_data('itemData'))?>,
                    variableElements : <?=json_encode(get_data('contentVariableElements'))?>,
                    userVars : <?=json_encode(get_data('js_variables'))?>,
                    customScripts : <?=json_encode(get_data('javascripts'))?>
                };
            }());
        </script>

        <?php if(tao_helpers_Mode::is('production')):?>
            <script type="text/javascript" src="<?=get_data('taoQtiItem_lib_path')?>qtiLoader.min.js"></script>
        <?php else:?>
            <script type="text/javascript" src="<?=get_data('tao_lib_path')?>require.js" data-main="<?=get_data('taoQtiItem_lib_path')?>qtiLoader"></script>
        <?php endif;?>

    </head>
    <body>
        <div id="qti_item"></div>
    </body>
</html>
