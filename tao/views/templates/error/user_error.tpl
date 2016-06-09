<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error 500 - Internal Server Error</title>
    <link rel="stylesheet" href="<?= ROOT_URL ?>tao/views/css/tao-main-style.css">
    <link rel="stylesheet" href="<?= ROOT_URL ?>tao/views/css/error-pages.css">
</head>

<body>

<div class="content-wrap">
    <header class="dark-bar clearfix">
        <a href="<?= ROOT_URL ?>" class="lft" target="_blank">
            <img src="<?= ROOT_URL ?>tao/views/img/tao-logo.png" alt="TAO Logo" id="tao-main-logo">
        </a>
		<h1>Error 500 - Internal Server Error</h1>
    </header>

    <div class="section-container">
    	<div class="error-code">500</div>
    	<div class="error-text">			
			<?php if (!empty($message)): ?>
			<p><?= $message ?></p>
			<?php endif; ?>
	    </div>	    
		<ul class="plain links">
		<?php if (!empty($returnLink)): ?>
			<?php if (!empty($_SERVER['HTTP_REFERER'])) : ?>				
				<li><a href="<?= $_SERVER['HTTP_REFERER'] ?>"><?=__('Go Back')?></a></li>
			<?php endif; ?>
			<li><a href="<?= ROOT_URL ?>"><?=__('TAO Home')?></a></li>
		<?php endif; ?>
		</ul>

    </div>
</div>

<footer class="dark-bar">
    © 2013 - 2015 · <a href="http://taotesting.com" target="_blank">Open Assessment Technologies S.A.</a>
    · All rights reserved.
</footer>

</body>
</html>