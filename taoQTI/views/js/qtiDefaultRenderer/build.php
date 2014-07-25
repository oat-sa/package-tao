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
 * 
 */
$dirname = dirname(__FILE__);
include_once($dirname."/../../../../tao/lib/jstools/minify.php");

//minimify QTI Javascript sources using JSMin
$jsFiles = array(
    $dirname.'/src/ResultCollector.js',
    $dirname.'/src/class.DefaultRenderer.js',
    $dirname.'/src/widgets/class.Widget.js',
    $dirname.'/src/widgets/class.Object.js',
    $dirname.'/src/widgets/class.StringInteraction.js',
    $dirname.'/src/widgets/class.GraphicInteraction.js',
    $dirname.'/src/widgets/class.AssociateInteraction.js',
    $dirname.'/src/widgets/class.ChoiceInteraction.js',
    $dirname.'/src/widgets/class.EndAttemptInteraction.js',
    $dirname.'/src/widgets/class.ExtendedTextInteraction.js',
    $dirname.'/src/widgets/class.GapMatchInteraction.js',
    $dirname.'/src/widgets/class.GraphicAssociateInteraction.js',
    $dirname.'/src/widgets/class.GraphicGapMatchInteraction.js',
    $dirname.'/src/widgets/class.GraphicOrderInteraction.js',
    $dirname.'/src/widgets/class.HotspotInteraction.js',
    $dirname.'/src/widgets/class.HottextInteraction.js',
    $dirname.'/src/widgets/class.InlineChoiceInteraction.js',
    $dirname.'/src/widgets/class.MatchInteraction.js',
    $dirname.'/src/widgets/class.MediaInteraction.js',
    $dirname.'/src/widgets/class.OrderInteraction.js',
    $dirname.'/src/widgets/class.SelectPointInteraction.js',
    $dirname.'/src/widgets/class.SliderInteraction.js',
    $dirname.'/src/widgets/class.TextEntryInteraction.js',
    $dirname.'/src/widgets/class.UploadInteraction.js'
);
minifyJSFiles($jsFiles, $dirname."/qtiDefaultRenderer.min.js");

//minimify QTI CSS sources using JSMin
$cssFiles = array(
    $dirname.'/../../css/normalize.css',
    $dirname.'/../../css/base.css',
    $dirname.'/css/qti.css'
);
minifyCSSFiles($cssFiles, $dirname."/css/qtiDefaultRenderer.min.css");

exit(0);