<?php
use oat\tao\helpers\Template;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
	<head>
		<title><?=__("Workflow Engine")?></title>

		<style media="screen">
			@import url(<?=BASE_WWW?>/css/process_authoring.css);
		</style>

		<script type="text/javascript" src="<?=TAOBASE_WWW?>js/lib/jquery-1.8.0.min.js"></script>
	</head>
	<body>
  <div class="content-wrap">
		<ul id="control">
        	<li>
        		<span id="connecteduser" class="icon"><?=__("User Id.")?> <span id="username"><?=$userViewData['username']?></span> </span><span class="separator"></span>
        	</li>
         	<li>
         		<a class="action icon" id="home" href="<?=BASE_URL?>Main/index"><?=__("Home")?></a> <span class="separator"></span>
         	</li>
         	<li>
         		<a class="action icon" id="logout" href="<?=BASE_URL?>Authentication/logout"><?=__("Logout")?></a>
         	</li>
		</ul>

		<div id="content">
			<h1 id="authoring_title"><?=__("Process initialization")?></h1>

			<div id="business">
				<h2 id="authoring_subtitle"><?=$processAuthoringData['processLabel']?></h2>
				<form id="authoring_form" action="<?=_url('initProcessExecution','ProcessInstanciation')?>" method="post" >
				<input type="hidden" name="posted[executionOf]" value="<?=urlencode($processAuthoringData['processUri']); ?>"/>
					<table id="authoring_table">
						<tbody>
							<?foreach($processAuthoringData['variables'] as $var): ?>
							<tr>
								<td class="variable_name"><?=$var['name']?> :</td>
								<td><input type="text" size="50" name="posted[variables][<?=$var['key']?>]" value=""/></td>
							</tr>
							<?endforeach?>
						</tbody>
						<tfoot>
							<tr>
								<td id="authoring_submit" colspan="2"><input id="submit_process" type="submit" name="posted[new]" value="<?=__("Launch Process")?>"/></td>
							</tr>
						</tfoot>
					</table>

				</form>
			</div>

		</div>
        </div>
<!-- /content-wrap -->
<?php
Template::inc('layout_footer.tpl', 'tao')
?>
</body>
</html>