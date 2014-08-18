<?php
header("HTTP/1.0 404 Not Found");
?>
<html>
<head>
	<title>404 Not Found</title>
</head>
<body>
	<b>Error:</b>
	<p><?php echo isset($message)?$message:"Page not found"; ?></p>
</body>
</html>