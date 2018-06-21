<!DOCTYPE html>
<html>
<head>
	<title>Fehler</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<?= $this->Html->css('/css/bootstrap.min.css') ?>
	<?= $this->Html->css('/css/styles.css') ?>
</head>
<body>
<header></header>
<div class="container">
	<h1>Error</h1>
	<pre><?= $error ?></pre>
	<?php if (TESTING): ?>
		<pre>URL: <?= $url ?></pre>
		<pre><?= $trace ?></pre>
		<pre>POST: <?= print_r($_POST, true) ?></pre>
	<?php endif; ?>
</div>
</body>
</html>
