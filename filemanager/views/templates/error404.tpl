<?header("HTTP/1.0 404 Not Found");?>
<html>
<head>
	<title>404 Not Found</title>
</head>
<body>
	<p>The requested URL was not found on this server.</p>
	<p><?=isset($message)? $message : ''?></p>
</body>
</html>