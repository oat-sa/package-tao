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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?=get_data('title')?></title>

        <!-- CSS -->
        <?if(DEBUG_MODE):?>
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>css/qti.css" media="screen" />
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>../../css/normalize.css" media="screen" />
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>../../css/base.css" media="screen" />
        <?else:?>
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>css/qtiDefaultRenderer.min.css" media="screen" />
        <?endif;?>
        <?if(get_data('hasMedia')):?>
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>lib/mediaelement/css/mediaelementplayer.min.css" media="screen" />
        <?endif;?>
        <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_taobase_www')?>css/custom-theme/jquery-ui-1.8.22.custom.css" />

        <!-- user CSS -->
        <?foreach(get_data('stylesheets') as $stylesheet):?>
            <link rel="stylesheet" type="text/css" href="<?=$stylesheet['href']?>" media="<?=$stylesheet['media']?>" />
        <?endforeach?>

        <!-- LIB -->
        <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/jquery-ui-1.8.23.custom.min.js"></script>
        <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/json.min.js"></script>

        <?if(get_data('hasMath')):?>
            <!--math rendering-->
            <script type="text/javascript" src="<?=get_data('ctx_math_lib_www')?>MathJax.js?config=TeX-AMS-MML_HTMLorMML-full"></script>
        <?endif;?>
        <?if(get_data('hasGraphics')):?>
            <!--raphael graphic-->
            <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/raphael/raphael.min.js"></script>
            <?if(DEBUG_MODE):?>
                <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/raphael/raphael-collision/raphael-collision.js"></script>
            <?else:?>
                <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/raphael/raphael-collision.min.js"></script>
            <?endif;?>
        <?endif?>

        <?if(get_data('hasUpload')):?>
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_taobase_www')?>js/jquery.uploadify/uploadify.css" media="screen" />
            <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/jquery.uploadify/jquery.uploadify.v2.1.4.min.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/jquery.uploadify/swfobject.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/AsyncFileUpload.js"></script>
        <?endif?>

        <!-- JS REQUIRED -->
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>qtiItem.min.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiRunner_lib_www')?>qtiRunner.min.js"></script>
        <?if(get_data('hasMedia')):?>
            <!--Media Element Js lib for audio and video file rendering-->
            <script id="qti_script_mediaelement" type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>lib/mediaelement/mediaelement-and-player.min.js"></script>
        <?endif;?>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>qtiDefaultRenderer.min.js"></script>

        <!--start the runner-->
        <script type="text/javascript" src="<?=get_data('ctx_qtiRunner_lib_www')?>src/initTaoApis.js"></script>
        <script type="text/javascript">
            var qti_base_www = '';
            var root_url = "<?=get_data('ctx_root_url')?>";
            <?if(!is_null(get_data('ctx_debug')) && get_data('ctx_debug')):?>var qti_debug = true;<?endif?>
            var qti_plugin_path = '';
            if($('#qti_script_mediaelement').length){
                qti_plugin_path = $('#qti_script_mediaelement').attr('src').replace('mediaelement-and-player.min.js', '');//dynamically get the plugin path
            }
            var itemData = <?=tao_helpers_Javascript::buildObject(get_data('itemData'))?>;
        </script>
    </head>
    <body>
        <div id="qti_item"></div>
        <div class="qti_control">
            <?if(get_data('ctx_raw_preview')):?>
                <a href="#" id="qti_validate" style="visibility:hidden;"><?=__("Submit");?></a>
            <?else:?>
                <a href="#" id="qti_validate"><?=__("Submit");?></a>
            <?endif?>
        </div>
        <div id="modalFeedbacks"></div>
    </body>
</html>