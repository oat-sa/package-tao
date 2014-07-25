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
    $dirname.'/lib/class.js',
    $dirname.'/src/class.ItemLoader.js',
    $dirname.'/src/class.Qti.js',
    $dirname.'/src/class.Element.js',
    $dirname.'/src/class.Math.js',
    $dirname.'/src/class.Object.js',
    $dirname.'/src/choices/class.Choice.js',
    $dirname.'/src/choices/class.ContainerChoice.js',
    $dirname.'/src/choices/class.SimpleChoice.js',
    $dirname.'/src/choices/class.SimpleAssociableChoice.js',
    $dirname.'/src/choices/class.Hottext.js',
    $dirname.'/src/choices/class.Hotspot.js',
    $dirname.'/src/choices/class.HotspotChoice.js',
    $dirname.'/src/choices/class.AssociableHotspot.js',
    $dirname.'/src/choices/class.TextVariableChoice.js',
    $dirname.'/src/choices/class.InlineChoice.js',
    $dirname.'/src/choices/class.GapText.js',
    $dirname.'/src/choices/class.GapImg.js',
    $dirname.'/src/choices/class.Gap.js',
    $dirname.'/src/interactions/class.Interaction.js',
    $dirname.'/src/interactions/class.InlineInteraction.js',
    $dirname.'/src/interactions/class.EndAttemptInteraction.js',
    $dirname.'/src/interactions/class.InlineChoiceInteraction.js',
    $dirname.'/src/interactions/class.TextEntryInteraction.js',
    $dirname.'/src/interactions/class.Prompt.js',
    $dirname.'/src/interactions/class.BlockInteraction.js',
    $dirname.'/src/interactions/class.AssociateInteraction.js',
    $dirname.'/src/interactions/class.ChoiceInteraction.js',
    $dirname.'/src/interactions/class.ExtendedTextInteraction.js',
    $dirname.'/src/interactions/class.MatchInteraction.js',
    $dirname.'/src/interactions/class.OrderInteraction.js',
    $dirname.'/src/interactions/class.SliderInteraction.js',
    $dirname.'/src/interactions/class.UploadInteraction.js',
    $dirname.'/src/interactions/class.ContainerInteraction.js',
    $dirname.'/src/interactions/class.GapMatchInteraction.js',
    $dirname.'/src/interactions/class.HottextInteraction.js',
    $dirname.'/src/interactions/class.ObjectInteraction.js',
    $dirname.'/src/interactions/class.MediaInteraction.js',
    $dirname.'/src/interactions/class.GraphicInteraction.js',
    $dirname.'/src/interactions/class.GraphicAssociateInteraction.js',
    $dirname.'/src/interactions/class.GraphicGapMatchInteraction.js',
    $dirname.'/src/interactions/class.GraphicOrderInteraction.js',
    $dirname.'/src/interactions/class.HotspotInteraction.js',
    $dirname.'/src/interactions/class.SelectPointInteraction.js',
    $dirname.'/src/feedbacks/class.Feedback.js',
    $dirname.'/src/feedbacks/class.FeedbackBlock.js',
    $dirname.'/src/feedbacks/class.FeedbackInline.js',
    $dirname.'/src/feedbacks/class.ModalFeedback.js',
    $dirname.'/src/class.Container.js',
    $dirname.'/src/class.Item.js',
    $dirname.'/src/class.ResponseDeclaration.js',
    $dirname.'/src/class.OutcomeDeclaration.js',
);
minifyJSFiles($jsFiles, $dirname."/qtiItem.min.js");