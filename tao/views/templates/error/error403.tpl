<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error 403 - <?=__('Access Denied')?></title>
    <link rel="stylesheet" href="<?= ROOT_URL ?>tao/views/css/tao-main-style.css">
    <link rel="stylesheet" href="<?= ROOT_URL ?>tao/views/css/error-pages.css">
</head>

<body>

<div class="content-wrap">
    <header class="dark-bar clearfix">
        <a href="<?= ROOT_URL ?>" class="lft" target="_blank">
            <img src="<?= ROOT_URL ?>tao/views/img/tao-logo.png" alt="TAO Logo" id="tao-main-logo">
        </a>
        <h1>Error 403 - <?=__('Access Denied')?></h1>
    </header>

    <div class="section-container">
        <div class="error-code">403</div>
        <div class="error-text">
            <p>You are not authorised to use the requested feature.</p>
            <p>If you think you should have access, please</p>
            <ul>
                <li>try again later</li>
                <li>or contact your TAO administrator to request access.</li>
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