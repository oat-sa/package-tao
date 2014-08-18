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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?=$label?></title>

	<!-- LIB -->
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/lib/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/lib/jquery-ui-1.8.23.custom.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/lib/json.min.js"></script>
	<script type="text/javascript">
		var root_url = "<?=$ctx_root_url?>";
	</script>

	<!-- JS REQUIRED -->
	<?if(!$ctx_raw_preview):?>
	<script type="text/javascript" src="<?=$ctx_root_url?>/wfEngine/views/js/wfApi/wfApi.min.js"></script>
	<?endif?>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoApi/taoApi.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/taoMatching.min.js"></script>
</head>
<body>
	<div class="swf_item">
		<object
			id="tao_item"
			classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
			codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"
			width="700"
			height="550"
			align="middle">
				<param name="allowScriptAccess" value="sameDomain" />
				<param name="movie" value="<?=$runtime?>?localXmlFile=<?=$contentUrl?>&amp;instance=<?=$uri?>" />
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />
				<param name="wmode" value="transparent" />
				<embed
					src="<?=$runtime?>?localXmlFile=<?=$contentUrl?>&amp;instance=<?=$uri?>"
					quality="high"
					bgcolor="#ffffff"
					width="700"
					height="550"
					align="middle"
					wmode="transparent"
					allowScriptAccess="sameDomain"
					type="application/x-shockwave-flash"
					pluginspage="http://www.macromedia.com/go/getflashplayer"
				 />
		</object>
	</div>

</body>
</html>
