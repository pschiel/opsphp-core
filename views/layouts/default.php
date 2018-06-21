<!DOCTYPE html>
<html>
<head>
	<title><?= $page_title ?></title>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
</head>
<body>

	<div id="page" class="clearfix">
		<div id="header">
			<?= $this->element('menu') ?>
		</div>
		<div id="main">
			<?= Session::flash() ?>
			<?= $content ?>
		</div>
	</div>

</body>
</html>