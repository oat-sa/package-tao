<?php use oat\tao\helpers\Template; ?>
<h1><?=__('Tests')?></h1>

<p><?=__('Build and manage tests using items that you have previously created or imported.')?></p>

<h2><?=__('Create QTI Tests')?></h2>

<p><?=__('You can create tests in compliance with the QTI 2.1 standard. A QTI test is structured as follows:')?><br />
&nbsp;&nbsp;<em><?=__('Test')?> &gt; <?=__('Test part(s)')?> &gt; <?=__('Test section(s)')?> &gt; <?=__('Item(s)')?></em>
</p>

<p><?=__('For example:')?>
    <img src="<?=Template::img('qtitest.png', 'taoCe')?>" alt="<?=__('QTI Test Structure')?>"  /><br />
    <em><?=__('Hint: "Simple” tests without any hierarchy are also available. Note that Open Web Items (OWI) can be delivered in simple test mode only.')?></em>
</p>

<p><?=__('To create a QTI test:')?>
    <ul>
        <li><?=__('Build your test by dragging and dropping the selected items into the test pane.')?></li>
        <li><?=__('Determine the order of your items. Items can be displayed either randomly or in a specific order.')?></li>
    </ul>
</p>

<h2><?=__('Define the Test Parameters')?></h2>

<p><?=__('You can specify test settings such as:')?>
    <ul>
        <li><?=__('Time limit')?></li>
        <li><?=__('Order of items – sequential or random')?></li>
    </ul>
</p>

<h2><?=__('Import / Export Tests')?></h2>

<p><?=__('You can import tests created with any external tool that is QTI compliant. The items will be automatically added to the Items Library. Conversely, your QTI tests can be exported out of TAO for use with any QTI-compliant delivery engine.')?></p>
