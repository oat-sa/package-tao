<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error 404 - Page Not Found</title>
    <link rel="stylesheet" href="<?= ROOT_URL ?>tao/views/css/tao-main-style.css">
    <link rel="stylesheet" href="<?= ROOT_URL ?>tao/views/css/error-pages.css">
</head>

<body>

<div class="content-wrap">
    <header class="dark-bar clearfix">
        <a href="<?= ROOT_URL ?>" class="lft" target="_blank">
            <img src="<?= ROOT_URL ?>tao/views/img/tao-logo.png" alt="TAO Logo" id="tao-main-logo">
        </a>
		<h1>Error 404 - Page Not Found</h1>
    </header>

    <div class="section-container">
    	<div class="error-code">404</div>
    	<div class="error-text">
	    	<p>The page you requested was not found on this server.</p>
			<ul>
			    <li>Verify the address you entered in your web browser is valid.</li>
			    <li>If you are sure that the address is correct but this page is still displayed contact your TAO administrator.</li>
		    </ul>
	    </div>	    
		<ul class="plain links">
			<?php if (!empty($_SERVER['HTTP_REFERER'])) : ?>
			<li><a href="<?= $_SERVER['HTTP_REFERER'] ?>"><?=__('Go Back')?></a></li>
			<?php endif; ?>
			<li><a href="<?= ROOT_URL ?>"><?=__('TAO Home')?></a></li>
		</ul>

	    <?php if (defined('DEBUG_MODE') && DEBUG_MODE == true): ?>
	    	<?php if (!empty($message)): ?>
	    		<h2>Debug Message</h2>    		
	    		<pre><?= $message ?></pre>
	    	<?php endif; ?>
	    	
	    	<?php if (!empty($trace)): ?>
	    		<h2>Stack Trace</h2>    		
	    		<pre><?= $trace ?></pre>
	    	<?php endif; ?>
	    <?php endif; ?>

    </div>
</div>

<footer class="dark-bar">
    © 2013 - 2015 · <a href="http://taotesting.com" target="_blank">Open Assessment Technologies S.A.</a>
    · All rights reserved.
</footer>

</body>
</html>