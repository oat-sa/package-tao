<?php
header("HTTP/1.0 503 Internal Error");
?>
<html>
<head>
	<title>503 Internal Error</title>
</head>
<body>
	<p><strong><?php echo __("An error occured during request processing") ?></strong></p>
	<p><?=(isset($message))?$message:""?></p>
</body>
</html>