<?php 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="title" content="TAO platform">
  <meta name="author" content="Administrator">
  <meta name="description" content="TAO, Testing assist&eacute; par ordinateur, computer based testing, evaluation, assessment, CBT, CAT, elearning, competencies, comp&eacute;tences">
  <meta name="keywords" content="TAO, Testing assist&eacute; par ordinateur, computer based testing, evaluation, assessment, CBT, CAT, elearning, competencies, comp&eacute;tences">
  <meta name="robots" content="index, follow">
  <title>TAO - An Open and Versatile Computer-Based Assessment Platform</title>

	<style type="text/css">
		body {background: #CDCDCD;color: #022E5F; font-family: verdana, arial, sans-serif;}
		td {font-size: 14px;}
		a {text-decoration: none;font-weight: bold; border: none; color: #BA122B;}
		a:hover {text-decoration: underline; border: none; color: #BA122B;}
		//table {width:759px; height:569px; }
	</style>
	
	<script type="text/javascript">
	function validate_required(field,alerttxt){
		var val = document.getElementById(field).value;
		if (val==null||val==""){
			alert(alerttxt);
			document.getElementById(field).focus();
			return false;
		}
		else{
			return true;
		}
	}
	function validate_form(){
		if (validate_required("login", "<?=__("the login must not be empty")?>")==false){
			return false;
		}
		if (validate_required("password","<?=__("the password must not be empty")?>")==false){
			return false;
		}
		return true
	}
	</script>
	
</head>
<body>

<div align="center" style="position:relative; top:50px;">
	<form onsubmit="return validate_form()" method="post" >
	<table width="759px" height="569px" cellpadding="10" cellspacing="0" background="<?=BASE_WWW?>deliveryServer/bg_index.jpg" style="border:thin solid #022E5F;">
		<tr><td height="120px"></td></tr>
		<tr>
			<td>
				<table width="739px" cellpadding="4" cellspacing="3">
				<tr>
				<td width="40px"></td>
				<td colspan="3"><b>WELCOME to the TAO demo portal!</b></td>
				</tr>
				<tr>
				<td width="40px"></td>
				<td colspan="3"><b>Please log in to experience the possibilities within TAO:</b></td>
				</tr>
				<tr>
				<td colspan="4"></td>
				</tr>
				<tr>
				<td colspan="4"></td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td colspan="2"><b><?=get_data("login_message")?></b></td>
				</tr>
				<tr>
				<td width="40px"></td>
				<td width="350px"></td>
				<td align="left"><b>Login</b></td>
				<td align="left"><input type="text" name="login" id="login" /></td>
				</tr>
				<tr>
				<td colspan="2"></td>
				<td align="left"><b>Password</b></td>
				<td align="left"><input type="password" name="password" id="password" /></td>
				</tr>
				<tr>
				<td colspan="3"></td>
				<td align="left"><input type="submit" id="submit" value="  login  " /></td>
				</tr>			
				</table>
			</td>
		</tr>
	</table>
	</form>
 
	<div id="result">
		
	</div>

</div>

</body>
</html>
